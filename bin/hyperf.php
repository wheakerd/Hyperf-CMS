<?php
ini_set('display_errors', 'on');
ini_set('display_startup_errors', 'on');
ini_set('memory_limit', '1G');

error_reporting(E_ALL);

/**
 * @const string BASE_PATH /
 */
!defined('BASE_PATH') && define('BASE_PATH', dirname(__DIR__));

/**
 * @const string ROOT_PATH /
 */
!defined('ROOT_PATH') && define('ROOT_PATH', (function () {
	$phar_enable = Phar::running(false);
	return strlen($phar_enable) ? dirname($phar_enable) : BASE_PATH;
})());

/**
 * @const boolean ROOT_PATH /
 */
!defined('PHAR_ENABLE') && define('PHAR_ENABLE', !!strlen(Phar::running(false)));

require BASE_PATH . '/vendor/autoload.php';

!defined('SWOOLE_HOOK_FLAGS') && define('SWOOLE_HOOK_FLAGS', (fn() => PHAR_ENABLE ?
	SWOOLE_HOOK_TCP
	| SWOOLE_HOOK_UNIX
	| SWOOLE_HOOK_UDP
	| SWOOLE_HOOK_UDG
	| SWOOLE_HOOK_SSL
	| SWOOLE_HOOK_TLS
	| SWOOLE_HOOK_SLEEP
	| SWOOLE_HOOK_STREAM_FUNCTION
	| SWOOLE_HOOK_BLOCKING_FUNCTION
	| SWOOLE_HOOK_PROC
	| SWOOLE_HOOK_NATIVE_CURL
	| SWOOLE_HOOK_SOCKETS
	| SWOOLE_HOOK_STDIO
	: Hyperf\Engine\DefaultOption::hookFlags())
());

// Self-called anonymous function that creates its own scope and keep the global namespace clean.
(function () {
	Hyperf\Di\ClassLoader::init();
	/** @var Psr\Container\ContainerInterface $container */
	$container = require BASE_PATH . '/config/container.php';

	$application = $container->get(Hyperf\Contract\ApplicationInterface::class);
	$application->run();
})();