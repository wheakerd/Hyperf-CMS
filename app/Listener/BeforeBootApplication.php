<?php
declare(strict_types=1);

namespace App\Listener;

use App\Cache\Admin\SystemDefaultCache;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\BootApplication;

/**
 * @BeforeBootApplication
 * @\App\Listener\BeforeBootApplication
 */
#[Listener]
final readonly class BeforeBootApplication implements ListenerInterface
{
	public function __construct(private SystemDefaultCache $systemDefaultCache)
	{
	}

	/**
	 * @return array
	 */
	public function listen(): array
	{
		return [
			BootApplication::class,
		];
	}

	/**
	 * @param object $event
	 *
	 * @return void
	 */
	public function process(object $event): void
	{
		//  初始化应用令牌密钥
		$this->systemDefaultCache->getAdminKey() || $this->systemDefaultCache->setAdminKey();
	}
}