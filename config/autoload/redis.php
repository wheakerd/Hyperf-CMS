<?php
declare(strict_types=1);

use function Hyperf\Support\env;

return [
	'default' => [
		'host'    => env('REDIS_HOST'),
		'auth'    => env('REDIS_AUTH'),
		'port'    => (int)env('REDIS_PORT'),
		'db'      => 0,
		'pool'    => [
			'min_connections' => 1,
			'max_connections' => 10,
			'connect_timeout' => 10.0,
			'wait_timeout'    => 3.0,
			'heartbeat'       => -1,
			'max_idle_time'   => (float)env('REDIS_MAX_IDLE_TIME', 60),
		],
		'options' => [
			'prefix' => env('REDIS_PREFIX', 'default_'),
		],
	],
	'user'    => [
		'host'    => env('REDIS_HOST'),
		'auth'    => env('REDIS_AUTH'),
		'port'    => (int)env('REDIS_PORT'),
		'db'      => 1,
		'pool'    => [
			'min_connections' => 1,
			'max_connections' => 10,
			'connect_timeout' => 10.0,
			'wait_timeout'    => 3.0,
			'heartbeat'       => -1,
			'max_idle_time'   => (float)env('REDIS_MAX_IDLE_TIME', 60),
		],
		'options' => [
			'prefix' => env('REDIS_PREFIX', 'default_'),
		],
	],
];