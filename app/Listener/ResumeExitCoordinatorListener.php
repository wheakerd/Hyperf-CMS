<?php
declare(strict_types=1);

namespace App\Listener;

use Hyperf\Command\Event\AfterExecute;
use Hyperf\Coordinator\Constants;
use Hyperf\Coordinator\CoordinatorManager;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;

/**
 * @ResumeExitCoordinatorListener
 * @\App\Listener\ResumeExitCoordinatorListener
 */
#[Listener]
final class ResumeExitCoordinatorListener implements ListenerInterface
{
	public function listen(): array
	{
		return [
			AfterExecute::class,
		];
	}

	public function process(object $event): void
	{
		CoordinatorManager::until(Constants::WORKER_EXIT)->resume();
	}
}
