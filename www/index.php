<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli' && isset($_SERVER['SERVER_NAME'])) {
	$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
		. '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

	if (preg_match('/^(https?:\/\/)(?:www\.)?([a-z0-9-.]+)(\/?.*)$/', $url, $urlParser)
		&& $urlParser[2] !== 'vikitron.com' && $urlParser[2] !== 'localhost'
	) {
		header('HTTP/1.1 301 Moved Permanently');
		header('Location: https://vikitron.com' . $urlParser[3]);
		die;
	}
}

if (PHP_SAPI !== 'cli' && (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off')) {
	$location = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	if ($_SERVER['HTTP_HOST'] !== 'localhost') {
		header('HTTP/1.1 301 Moved Permanently');
		header('Location: ' . preg_replace('/^(https:\/\/)(?:www\.)(.*)$/', '$1$2', $location));
		die;
	}
}

require __DIR__ . '/../vendor/autoload.php';

App\Booting::boot()
	->createContainer()
	->getByType(Nette\Application\Application::class)
	->run();
