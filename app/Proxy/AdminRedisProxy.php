<?php
declare(strict_types=1);

namespace App\Proxy;

use Hyperf\Redis\Redis;

/**
 * @AdminRedisProxy
 * @\App\Proxy\AdminRedisProxy
 */
final class AdminRedisProxy extends Redis
{
	protected string $poolName = 'admin';
}