<?php
declare(strict_types=1);

namespace App\Cache\Admin;

use App\Abstract\AbstractCache;
use App\Library\JsonWebToken\JWK;
use App\Proxy\DefaultRedisProxy;

/**
 * 系统默认缓存类【应当在 BootApplication 事件前初始化】
 *
 * @SystemDefaultCache
 * @\App\Cache\Admin\SystemDefaultCache
 */
final class SystemDefaultCache extends AbstractCache
{
	public function __construct(DefaultRedisProxy $redis)
	{
		parent::__construct($redis);
	}

	public function setAdminKey(?string $key = null): void
	{
		$this->redis->set('admin_key', $key ?? JWK::createOctKey()->get('k'));
	}

	public function getAdminKey(): false|string
	{
		return $this->redis->get('admin_key');
	}
}