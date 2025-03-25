<?php
declare(strict_types=1);

namespace App\Listener;

use Hyperf\Collection\Arr;
use Hyperf\Database\Events\QueryExecuted;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Logger\LoggerFactory;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;

/**
 * @DbQueryExecutedListener
 * @\App\Listener\DbQueryExecutedListener
 */
#[Listener]
final class DbQueryExecutedListener implements ListenerInterface
{
	private LoggerInterface $logger;

	/**
	 * @param ContainerInterface $container
	 *
	 * @throws ContainerExceptionInterface
	 * @throws NotFoundExceptionInterface
	 */
	public function __construct(ContainerInterface $container)
	{
		$this->logger = $container->get(LoggerFactory::class)->get('sql');
	}

	public function listen(): array
	{
		return [
			QueryExecuted::class,
		];
	}

	/**
	 * @param QueryExecuted $event
	 */
	public function process($event): void
	{
		if ($event instanceof QueryExecuted) {
			$sql = $event->sql;
			if (!Arr::isAssoc($event->bindings)) {
				$position = 0;
				foreach ($event->bindings as $value) {
					$position = strpos($sql, '?', $position);
					if ($position === false) {
						break;
					}
					$value    = "'$value'";
					$sql      = substr_replace($sql, $value, $position, 1);
					$position += strlen($value);
				}
			}

			$this->logger->info(
				sprintf('[%s] %s', $event->time, $sql),
			);
		}
	}
}
