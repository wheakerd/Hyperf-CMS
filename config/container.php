<?php
/**
 * Initialize a dependency injection container that implemented PSR-11 and return the container.
 */

declare(strict_types=1);

use Hyperf\Context\ApplicationContext;
use Hyperf\Di\Container;
use Hyperf\Di\Definition\DefinitionSourceFactory;

/** @noinspection PhpUnhandledExceptionInspection */
$container = new Container(
	(
	new DefinitionSourceFactory()
	)(),
);

return ApplicationContext::setContainer($container);