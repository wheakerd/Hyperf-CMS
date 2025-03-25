<?php
declare(strict_types=1);

return [
	'default'         => [
		'handler'   => [
			'class'       => Monolog\Handler\RotatingFileHandler::class,
			'constructor' => [
				'filename' => ROOT_PATH . '/runtime/logs/hyperf/hyperf.log',
				'level'    => Monolog\Level::Error,
				'maxFiles' => 0,
			],
		],
		'formatter' => [
			'class'       => Monolog\Formatter\LineFormatter::class,
			'constructor' => [
				'format'                => null,
				'dateFormat'            => 'Y-m-d H:i:s',
				'allowInlineLineBreaks' => true,
			],
		],
	],
	'sql'             => [
		'handler'   => [
			'class'       => Monolog\Handler\RotatingFileHandler::class,
			'constructor' => [
				'filename' => ROOT_PATH . '/runtime/logs/sql/sql.log',
				'level'    => Monolog\Level::Error,
				'maxFiles' => 0,
			],
		],
		'formatter' => [
			'class'       => Monolog\Formatter\LineFormatter::class,
			'constructor' => [
				'format'                => null,
				'dateFormat'            => 'Y-m-d H:i:s',
				'allowInlineLineBreaks' => true,
			],
		],
	],
	'tiktok'          => [
		'handler'   => [
			'class'       => Monolog\Handler\RotatingFileHandler::class,
			'constructor' => [
				'filename' => ROOT_PATH . '/runtime/logs/tiktok/tiktok.log',
				'level'    => Monolog\Level::Error,
				'maxFiles' => 0,
			],
		],
		'formatter' => [
			'class'       => Monolog\Formatter\LineFormatter::class,
			'constructor' => [
				'format'                => null,
				'dateFormat'            => 'Y-m-d H:i:s',
				'allowInlineLineBreaks' => true,
			],
		],
	],
	'quick-worker'    => [
		'handler'   => [
			'class'       => Monolog\Handler\RotatingFileHandler::class,
			'constructor' => [
				'filename' => ROOT_PATH . '/runtime/logs/quick-worker/quick-worker.log',
				'level'    => Monolog\Level::Error,
				'maxFiles' => 0,
			],
		],
		'formatter' => [
			'class'       => Monolog\Formatter\LineFormatter::class,
			'constructor' => [
				'format'                => null,
				'dateFormat'            => 'Y-m-d H:i:s',
				'allowInlineLineBreaks' => true,
			],
		],
	],
	'magnetic-engine' => [
		'handler'   => [
			'class'       => Monolog\Handler\RotatingFileHandler::class,
			'constructor' => [
				'filename' => ROOT_PATH . '/runtime/logs/magnetic-engine/magnetic-engine.log',
				'level'    => Monolog\Level::Error,
				'maxFiles' => 0,
			],
		],
		'formatter' => [
			'class'       => Monolog\Formatter\LineFormatter::class,
			'constructor' => [
				'format'                => null,
				'dateFormat'            => 'Y-m-d H:i:s',
				'allowInlineLineBreaks' => true,
			],
		],
	],
];