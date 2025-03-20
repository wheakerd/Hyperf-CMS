<?php
declare(strict_types=1);

use function Hyperf\Support\env;

return [
	'default' => [
		'driver'    => env('DB_DRIVER'),
		'host'      => env('DB_HOST'),
		'port'      => env('DB_PORT'),
		'database'  => env('DB_DATABASE'),
		'username'  => env('DB_USERNAME'),
		'password'  => env('DB_PASSWORD'),
		'charset'   => env('DB_CHARSET'),
		'collation' => env('DB_COLLATION'),
		'prefix'    => env('DB_PREFIX'),
		'pool'      => [
			'min_connections' => (integer)env('DB_MIN_CONNECTIONS', 1),
			'max_connections' => (integer)env('DB_MAX_CONNECTIONS', 10),
			'connect_timeout' => (float)env('DB_CONNECT_TIMEOUT', 10.0),
			'wait_timeout'    => (float)env('DB_WAIT_TIMEOUT', 3.0),
			'heartbeat'       => (integer)env('DB_HEARTBEAT', -1),
			'max_idle_time'   => (float)env('DB_MAX_IDLE_TIME', 60),
		],
		'commands'  => [
			'gen:model' => [
				'path'             => 'app/Model',
				'force_casts'      => true,
				'inheritance'      => 'AbstractModel',
				'uses'             => App\Abstract\AbstractModel::class,
				'refresh_fillable' => true,
				'table_mapping'    => [],
				'with_comments'    => true,
				'property_case'    => Hyperf\Database\Commands\ModelOption::PROPERTY_CAMEL_CASE,
				'visitors'         => [
					Hyperf\Database\Commands\Ast\ModelRewriteInheritanceVisitor::class,
					Hyperf\Database\Commands\Ast\ModelRewriteKeyInfoVisitor::class,
					Hyperf\Database\Commands\Ast\ModelRewriteSoftDeletesVisitor::class,
				],
			],
		],
	],
];