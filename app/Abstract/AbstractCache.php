<?php
declare(strict_types=1);

namespace App\Abstract;

use Hyperf\Redis\Redis;
use RuntimeException;

/**
 * @AbstractCache
 * @\App\Abstract\AbstractCache
 */
abstract class AbstractCache
{
	public function __construct(protected readonly Redis $redis)
	{
	}

	/**
	 * 加锁或
	 *
	 * @param string $key
	 * @param int    $ttl 毫秒
	 *
	 * @return mixed
	 */
	protected function lock(string $key, int $ttl = 30): mixed
	{
		$lockKey   = "read_lock:$key";
		$lockValue = uniqid(more_entropy: true);

		// 加锁（过期时间 3 秒，防止死锁）
		if (!$this->redis->set($lockKey, $lockValue, [
			'NX',
			'PX' => $ttl,
		])) {
			throw new RuntimeException('Failed to acquire read lock');
		}

		try {
			// 读取数据
			$value = $this->redis->get($key);
		}
		finally {
			// 释放锁（防止误删，确保是自己加的锁）
			if ($this->redis->get($lockKey) === $lockValue) {
				$this->redis->del($lockKey);
			}
		}

		return $value;
	}
}