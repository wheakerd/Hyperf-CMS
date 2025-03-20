<?php
declare(strict_types=1);

return [
	'scan' => [
		'paths'              => [
			BASE_PATH . '/app',
			BASE_PATH . '/extend',
		],
		'ignore_annotations' => [
			'assets',
			'mixin',
		],
	],
];