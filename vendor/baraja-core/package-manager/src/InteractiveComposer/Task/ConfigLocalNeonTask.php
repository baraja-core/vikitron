<?php

declare(strict_types=1);

namespace Baraja\PackageManager\Composer;


use Baraja\PackageManager\Helpers;
use Nette\Neon\Neon;

class ConfigLocalNeonTask extends BaseTask
{

	/**
	 * @var string[][]
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
	 * @return bool
	 */
	public function run(): bool
	{
		$path = \dirname(__DIR__, 6) . '/app/config/local.neon';

		if (\is_file($path)) {
			echo 'local.neon exist.' . "\n";
			echo 'Path: ' . $path;

			return true;
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

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return 'Local.neon checker';
	}

	/**
	 * @return mixed[]
	 */
	private function generateMySqlConfig(): array
	{
		$mySqlCredentials = $this->mySqlConnect();
		$connection = new \PDO(
			'mysql:host=' . $mySqlCredentials['server'],
			$mySqlCredentials['user'],
			$mySqlCredentials['password']
		);

		$databases = $connection->query('SHOW DATABASES')->fetchAll();
		$databaseList = [];
		$databaseCounter = 1;
		$usedDatabase = null;

		foreach ($databases as $database) {
			echo $databaseCounter . ': ' . $database[0] . "\n";
			$databaseList[$databaseCounter] = $database[0];
			$databaseCounter++;
		}

		while (true) {
			$usedDatabase = $this->ask('Which database use? Type "new" for create new.');

			if (preg_match('/^\d+$/', $usedDatabase)) {
				$usedDatabaseKey = (int) $usedDatabase;
				if (isset($databaseList[$usedDatabaseKey])) {
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
					$usedDatabase = $this->ask('How is the database name?');

					if (preg_match('/^[a-z0-9\_\-]+$/', $usedDatabase)) {
						if (!\in_array($usedDatabase, $databaseList, true)) {
							$this->createDatabase($usedDatabase, $connection);
							break;
						}

						echo 'Database "' . $usedDatabase . '" already exist.' . "\n\n";
					} else {
						echo 'Invalid database name. You can use only a-z, 0-9, "-" and "_".';
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
				$newDatabaseName = strtolower($usedDatabase);

				if ($this->ask('Create database "' . $newDatabaseName . '"?', ['y', 'n']) === 'y') {
					$this->createDatabase($newDatabaseName, $connection);
					break;
				}
			}

			echo 'Invalid database selection. Please use number in range (1 - ' . ($databaseCounter - 1) . ') or NEW.';
		}

		return [
			'parameters' => [
				'database' => [
					'primary' => [
						'host' => $mySqlCredentials['server'],
						'dbname' => $usedDatabase,
						'user' => $mySqlCredentials['user'],
						'password' => $mySqlCredentials['password'],
					],
				],
			],
		];
	}

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

		while (true) {
			$connectionServer = $this->ask('Server (hostname):');
			$connectionUser = $this->ask('User:');
			$connectionPassword = $this->ask('Password');

			try {
				new \PDO('mysql:host=' . $connectionServer, $connectionUser, $connectionPassword);

				return [
					'server' => $connectionServer,
					'user' => $connectionUser,
					'password' => $connectionPassword,
				];
			} catch (\PDOException $e) {
				echo 'Connection does not work.';
			}
		}

		return [];
	}

	/**
	 * @param string $name
	 * @param \PDO $connection
	 */
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

}