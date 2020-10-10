<?php

declare(strict_types=1);

namespace Baraja\PackageManager\Composer;


use Baraja\PackageManager\Helpers;
use Baraja\PackageManager\PackageRegistrator;
use Nette\Neon\Neon;

/**
 * Priority: 1000
 */
final class ConfigLocalNeonTask extends BaseTask
{

	/**
	 * This credentials will be automatically used for test connection.
	 * If connection works it will be used for final Neon configuration.
	 *
	 * @var string[][][]
	 */
	private static $commonCredentials = [
		'localhost' => [
			['root', 'root'],
			['root', 'password'],
			['root', ''],
		],
		'127.0.0.1' => [
			['root', 'root'],
			['root', 'password'],
			['root', ''],
		],
	];


	/**
	 * Create configuration local.neon file with minimal default settings.
	 *
	 * 1. Check if local.neon exist
	 * 2. In case of CI environment generate default content
	 * 3. In other cases as user for configuration data
	 *
	 * @return bool
	 */
	public function run(): bool
	{
		if (\is_file($path = \dirname(__DIR__, 6) . '/app/config/local.neon') === true) {
			echo 'local.neon exist.' . "\n";
			echo 'Path: ' . $path;

			return true;
		}
		if (\class_exists('\Baraja\Doctrine\EntityManager') === false) {
			file_put_contents($path, '');

			return true;
		}

		try {
			if (PackageRegistrator::getCiDetect() !== null) {
				echo 'CI environment detected: Use default configuration.' . "\n";
				echo 'Path: ' . $path;
				file_put_contents($path, Neon::encode($this->getDefaultTestConfiguration(), Neon::BLOCK));

				return true;
			}
		} catch (\Exception $e) {
		}

		echo 'local.neon does not exist.' . "\n";
		echo 'Path: ' . $path;

		if ($this->ask('Create?', ['y', 'n']) === 'y') {
			file_put_contents($path, Neon::encode(
				$this->generateMySqlConfig(),
				Neon::BLOCK
			));
		}

		return true;
	}


	public function getName(): string
	{
		return 'Local.neon checker';
	}


	/**
	 * @return mixed[]
	 */
	private function generateMySqlConfig(): array
	{
		$connection = new \PDO(
			'mysql:host=' . ($mySqlCredentials = $this->mySqlConnect())['server'],
			$mySqlCredentials['user'],
			$mySqlCredentials['password']
		);

		$databaseList = [];
		$databaseCounter = 1;
		$usedDatabase = null;
		$candidateDatabases = [];
		echo "\n\n";

		foreach ($connection->query('SHOW DATABASES')->fetchAll() as $database) {
			echo $databaseCounter . ': ' . $database[0] . "\n";
			$databaseList[$databaseCounter] = $database[0];
			$databaseCounter++;
			if ($database[0] !== 'information_schema') {
				$candidateDatabases[] = $database[0];
			}
		}

		if (\count($candidateDatabases) === 1) {
			$usedDatabase = $candidateDatabases[0];
		}

		while (true) {
			if ($usedDatabase === null && preg_match('/^\d+$/', $usedDatabase = $this->ask('Which database use? Type number or specific name. Type "new" for create new.') ?? '')) {
				if (isset($databaseList[$usedDatabaseKey = (int) $usedDatabase])) {
					$usedDatabase = $databaseList[$usedDatabaseKey];
					break;
				}

				echo 'Selection "' . $usedDatabase . '" is out of range.' . "\n";
			}

			if (\in_array($usedDatabase, $databaseList, true)) {
				break;
			}

			if (strtolower($usedDatabase) === 'new') {
				while (true) {
					if (preg_match('/^[a-z0-9\_\-]+$/', $usedDatabase = $this->ask('How is the database name?'))) {
						if (!\in_array($usedDatabase, $databaseList, true)) {
							$this->createDatabase($usedDatabase, $connection);
							break;
						}

						echo 'Database "' . $usedDatabase . '" already exist.' . "\n\n";
					} else {
						Helpers::terminalRenderError('Invalid database name. You can use only a-z, 0-9, "-" and "_".');
						echo "\n\n";
					}
				}
				break;
			}

			if (preg_match('/^[a-zA-Z0-9\_\-]+$/', $usedDatabase)) {
				$useHint = false;
				foreach ($databaseList as $possibleDatabase) {
					if (strncmp($possibleDatabase, $usedDatabase, strlen($usedDatabase)) === 0) {
						$checkDatabase = $possibleDatabase;
						if ($this->ask('Use database "' . $checkDatabase . '"?', ['y', 'n']) === 'y') {
							$usedDatabase = $checkDatabase;
							$useHint = true;
							break;
						}
					}
				}

				if ($useHint === true) {
					break;
				}

				echo 'Database "' . $usedDatabase . '" does not exist.' . "\n";

				if ($this->ask('Create database "' . ($newDatabaseName = strtolower($usedDatabase)) . '"?', ['y', 'n']) === 'y') {
					$this->createDatabase($newDatabaseName, $connection);
					break;
				}
			}

			echo 'Invalid database selection. Please use number in range (1 - ' . ($databaseCounter - 1) . ') or NEW.';
		}

		return [
			'baraja.database' => [
				'connection' => [
					'host' => $mySqlCredentials['server'],
					'dbname' => $usedDatabase,
					'user' => $mySqlCredentials['user'],
					'password' => $mySqlCredentials['password'],
				],
			],
		];
	}


	/**
	 * Get mysql connection credentials and return fully works credentials or in case of error empty array.
	 *
	 * @return string[]
	 */
	private function mySqlConnect(): array
	{
		$dbh = null;
		$connectionServer = null;
		$connectionUser = null;
		$connectionPassword = null;

		foreach (self::$commonCredentials as $server => $credentials) {
			foreach ($credentials as $credential) {
				try {
					$dbh = new \PDO('mysql:host=' . $server, $credential[0], $credential[1]);
					$connectionServer = $server;
					[$connectionUser, $connectionPassword] = $credential;
					break;
				} catch (\PDOException $e) {
				}
			}
		}

		if ($dbh !== null) {
			echo '+--- Functional connections have been found automatically.' . "\n";
			echo '| Server: ' . \json_encode($connectionServer) . "\n";
			echo '| User: ' . \json_encode($connectionUser) . "\n";
			echo '| Password: ' . \json_encode($connectionPassword) . "\n";

			if ($this->ask('Use this configuration?', ['y', 'n']) === 'y') {
				return [
					'server' => $connectionServer,
					'user' => $connectionUser,
					'password' => $connectionPassword,
				];
			}
		}

		for ($ttl = 10; $ttl > 0; $ttl--) {
			if (($connectionServer = $this->ask('Server (hostname) [empty for "127.0.0.1"]:')) === null) {
				echo 'Server "127.0.0.1" has been used.';
				$connectionServer = '127.0.0.1';
			}
			if (($connectionUser = $this->ask('User [empty for "root"]:')) === null) {
				echo 'User "root" has been used.';
				$connectionUser = 'root';
			}
			while (($connectionPassword = trim($this->ask('Password [can not be empty!]:') ?? '')) === '') {
				Helpers::terminalRenderError('Password can not be empty!');
				echo "\n\n\n" . 'Information to resolve this issue:' . "\n\n";
				echo 'For the best protection of the web server and database,' . "\n";
				echo 'it is important to always set a passphrase that must not be an empty string.' . "\n";
				echo 'If you are using a database without a password, set the password first and then install again.';
			}
			echo "\n\n";

			try {
				new \PDO('mysql:host=' . $connectionServer, $connectionUser, $connectionPassword);

				return [
					'server' => $connectionServer,
					'user' => $connectionUser,
					'password' => $connectionPassword,
				];
			} catch (\PDOException $e) {
				Helpers::terminalRenderError('Connection does not work!');
				echo "\n";
				Helpers::terminalRenderError($e->getMessage());
				echo "\n\n";
			}
		}

		return [];
	}


	private function createDatabase(string $name, \PDO $connection): void
	{
		$sql = 'CREATE DATABASE IF NOT EXISTS `' . $name . '`; ' . "\n"
			. 'DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci';

		echo 'Creating database...' . "\n";
		echo 'Command: ' . $sql . "\n\n";

		if ($connection->exec($sql) !== 1) {
			Helpers::terminalRenderError('Can not create database!');
			echo "\n\n";

			return;
		}

		$checkSql = 'SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = \'' . $name . '\'';

		echo 'Database was successfully created.' . "\n\n";
		echo 'Testing database...' . "\n";
		echo 'Command: ' . $checkSql . "\n\n";

		if ($connection->exec($checkSql) === 1) {
			echo 'Database is OK.';
		} else {
			Helpers::terminalRenderError('Can not create database. Please create manually and return here.');
			echo "\n\n";
			die;
		}
	}


	/**
	 * Default configuration for CI and test environment.
	 *
	 * @return mixed[]
	 */
	private function getDefaultTestConfiguration(): array
	{
		return [
			'baraja.database' => [
				'connection' => [
					'host' => 'localhost',
					'dbname' => 'test',
					'user' => 'root',
					'password' => 'root',
				],
			],
		];
	}
}
