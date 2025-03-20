<?php
declare(strict_types=1);

use Hyperf\Server\Event;
use Hyperf\Server\ServerInterface;
use Swoole\Constant;

return [
	'mode'      => SWOOLE_PROCESS,
	'servers'   => [
		[
			'name'      => 'http',
			'type'      => ServerInterface::SERVER_HTTP,
			'host'      => '0.0.0.0',
			'port'      => 9500,
			'sock_type' => SWOOLE_SOCK_TCP,
			'callbacks' => [
				Event::ON_REQUEST => [
					Hyperf\HttpServer\Server::class,
					'onRequest',
				],
			],
			'options'   => [
				// Whether to enable request lifecycle event
				'enable_request_lifecycle' => true,
			],
		],
	],
	'settings'  => [
		Constant::OPTION_DAEMONIZE                => false,
		Constant::OPTION_HEARTBEAT_IDLE_TIME      => 60,
		Constant::OPTION_HEARTBEAT_CHECK_INTERVAL => 30,
		Constant::OPTION_ENABLE_COROUTINE         => true,
		Constant::OPTION_WORKER_NUM               => swoole_cpu_num(),
		Constant::OPTION_PID_FILE                 => ROOT_PATH . '/runtime/hyperf.pid',
		Constant::OPTION_OPEN_TCP_NODELAY         => true,
		Constant::OPTION_MAX_COROUTINE            => 100000,
		Constant::OPTION_OPEN_HTTP2_PROTOCOL      => false,
		Constant::OPTION_MAX_REQUEST              => 100000,
		Constant::OPTION_SOCKET_BUFFER_SIZE       => 1024 * 1024 * 10,
		Constant::OPTION_BUFFER_OUTPUT_SIZE       => 1024 * 1024 * 10,
		Constant::OPTION_PACKAGE_MAX_LENGTH       => 1024 * 1024 * 10,
	],
	'callbacks' => [
		Event::ON_WORKER_START => [
			Hyperf\Framework\Bootstrap\WorkerStartCallback::class,
			'onWorkerStart',
		],
		Event::ON_PIPE_MESSAGE => [
			Hyperf\Framework\Bootstrap\PipeMessageCallback::class,
			'onPipeMessage',
		],
		Event::ON_WORKER_EXIT  => [
			Hyperf\Framework\Bootstrap\WorkerExitCallback::class,
			'onWorkerExit',
		],
	],
];