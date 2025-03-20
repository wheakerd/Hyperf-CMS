<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Contract\ResponseContract;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use function Hyperf\Support\env;

/**
 * @MiddlewareDeveloper
 * @\App\Middleware\Http\MiddlewareDeveloper
 */
final readonly class MiddlewareDeveloper implements MiddlewareInterface
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