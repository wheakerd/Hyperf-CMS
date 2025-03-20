<?php
declare(strict_types=1);

namespace App;

use App\Contract\ResponseContract;
use App\Exception\CustomMessageException;
use App\Exception\LibraryException;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\Annotation\ExceptionHandler;
use Hyperf\ExceptionHandler\ExceptionHandler as HyperfExceptionHandler;
use Hyperf\Validation\ValidationException;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * @AppExceptionHandler
 * @\App\Exception\Handler\AppExceptionHandler
 */
#[ExceptionHandler]
final class AppExceptionHandler extends HyperfExceptionHandler
{
	public function __construct(
		private readonly StdoutLoggerInterface $logger,
		private readonly ResponseContract      $response,
	)
	{
	}

	public function handle(Throwable $throwable, ResponseInterface $response): MessageInterface|ResponseInterface
	{
		if (!PHAR_ENABLE) {
			$this->logger->error(
				sprintf('%s[%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()),
			);
			$this->logger->error(
				$throwable->getTraceAsString(),
			);
			$this->logger->error(
				$throwable,
			);
		}

		if ($throwable instanceof ValidationException) {
			return $this->handleValidator($throwable);
		}

		if ($throwable instanceof LibraryException) {
			return $this->handleLibrary($throwable);
		}

		if ($throwable instanceof CustomMessageException) {
			return $this->handleCustom($throwable);
		}

		return $this->response->server('Internal Server Error.');
	}

	/**
	 * The method is a validator exception handler.
	 *
	 * @param ValidationException $validationException
	 *
	 * @return MessageInterface
	 */
	public function handleValidator(ValidationException $validationException): MessageInterface
	{
		$this->stopPropagation();
		$message = $validationException->validator->errors()->first();

		return $this->response->validator($message);
	}

	public function isValid(Throwable $throwable): bool
	{
		return true;
	}

	/**
	 * The method is a custom exception handler.
	 *
	 * @param CustomMessageException $customMessageException
	 *
	 * @return ResponseInterface
	 */
	private function handleCustom(CustomMessageException $customMessageException): ResponseInterface
	{
		$this->stopPropagation();
		$message = $customMessageException->getMessage();

		return $this->response->error($message);
	}

	/**
	 * The method is a library exception handler.
	 *
	 * @param LibraryException $libraryException
	 *
	 * @return ResponseInterface
	 */
	private function handleLibrary(LibraryException $libraryException): ResponseInterface
	{
		$this->stopPropagation();
		$message = $libraryException->getMessage();

		return $this->response->error($message);
	}
}