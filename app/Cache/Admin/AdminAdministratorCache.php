<?php
declare(strict_types=1);

namespace App\Cache\Admin;

use App\Abstract\AbstractCache;
use App\Proxy\AdminRedisProxy;

/**
 * @AdminAdministratorCache
 * @\App\Cache\Admin\AdminAdministratorCache
 */
final readonly class AdminAdministratorCache extends AbstractCache
{
	public function __construct(AdminRedisProxy $redis)
	{
		parent::__construct($redis, 'administrator_token');
	}

	private function getKey(string $key): string
	{
		return $this->prefix . ':' . $key;
	}

	/**
	 * 判断用户凭证是否由本系统签发
	 *
	 * @param string $token
	 *
	 * @return false|int
	 */
	public function hasToken(string $token): false|int
	{
		return $this->redis->get($this->getKey($token));
	}

	/**
	 * 存储生成的用户令牌
	 *
	 * @param string $token
	 * @param int    $userid
	 * @param int    $expireTime
	 *
	 * @return void
	 */
	public function setToken(string $token, int $userid, int $expireTime = 60 * 60 * 24 * 30): void
	{
		$this->redis->setex($this->getKey($token), $expireTime, $userid);
	}

	/**
	 * 移除用户凭证
	 *
	 * @param string $token
	 *
	 * @return void
	 */
	public function delToken(string $token): void
	{
		$this->redis->del($this->getKey($token));
	}
}