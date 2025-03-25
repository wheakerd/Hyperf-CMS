<?php
declare(strict_types=1);

return [
	'default' => [
		'driver'             => Hyperf\Cache\Driver\RedisDriver::class,
		'packer'             => Hyperf\Codec\Packer\PhpSerializerPacker::class,
		'prefix'             => 'c:',
		'skip_cache_results' => [],
	],
];