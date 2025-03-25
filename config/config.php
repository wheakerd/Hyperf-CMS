<?php
declare(strict_types=1);

use Hyperf\Contract\StdoutLoggerInterface;
use Psr\Log\LogLevel;
use function Hyperf\Support\env;

return [
	'app_name'                   => env('APP_NAME', 'skeleton'),
	'app_env'                    => env('APP_ENV', 'dev'),
	'scan_cacheable'             => false,
	StdoutLoggerInterface::class => [
		'log_level' => match (env('APP_ENV', 'dev')) {
			'prod'  => [
				LogLevel::ALERT,
				LogLevel::CRITICAL,
				LogLevel::EMERGENCY,
				LogLevel::ERROR,
				LogLevel::NOTICE,
				LogLevel::WARNING,
			],
			default => [
				LogLevel::ALERT,
				LogLevel::CRITICAL,
				LogLevel::DEBUG,
				LogLevel::EMERGENCY,
				LogLevel::ERROR,
				LogLevel::INFO,
				LogLevel::NOTICE,
				LogLevel::WARNING,
			],
		},
	],
];