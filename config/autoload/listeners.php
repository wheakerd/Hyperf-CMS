<?php
declare(strict_types=1);

return [
	Hyperf\ExceptionHandler\Listener\ErrorExceptionHandler::class,
	Hyperf\Command\Listener\FailToHandleListener::class,
];