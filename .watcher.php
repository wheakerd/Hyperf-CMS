<?php
declare(strict_types=1);

use Hyperf\Watcher\Driver\FswatchDriver;

return [
	'driver' => FswatchDriver::class,
	'bin'    => PHP_BINARY,
	'watch'  => [
		'dir'           => [
			'app',
			'config',
		],
		'file'          => [
			'.env',
			'composer.json',
			'composer.lock',
			'.watcher.php',
			'bin/hyperf.php',
		],
		'scan_interval' => 2000,
	],
	'ext'    => [
		'.php',
		'.env',
	],
];