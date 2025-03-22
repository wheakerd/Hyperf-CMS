<?php
declare(strict_types=1);

namespace App\Middleware\Http;

use App\Contract\ResponseContract;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use function Hyperf\Support\env;

/**
 * @DeveloperMiddleware
 * @\App\Middleware\Http\DeveloperMiddleware
 */
final readonly class DeveloperMiddleware implements MiddlewareInterface
{
	public function __construct(private ResponseContract $response)
	{
	}

	/**
	 * @param ServerRequestInterface  $request
	 * @param RequestHandlerInterface $handler
	 *
	 * @return ResponseInterface
	 */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		if ('dev' === env('APP_ENV')) {
			return $handler->handle($request);
		}

		return $this->response->error('此操作可能会影响系统运行，非开发环境下禁用');
	}
}