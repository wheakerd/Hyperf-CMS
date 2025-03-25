<?php
declare(strict_types=1);

namespace App\Middleware\Http;

use Hyperf\Context\Context;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @DomainCrossMiddleware
 * @\app\Middleware\Http\DomainCrossMiddleware
 */
final readonly class DomainCrossMiddleware implements MiddlewareInterface
{
	/**
	 * @param ServerRequestInterface  $request
	 * @param RequestHandlerInterface $handler
	 *
	 * @return ResponseInterface
	 */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$response = Context::get(ResponseInterface::class);

		$response = $response->withHeader('Access-Control-Allow-Origin', $request->getHeaderLine('origin') ?? '*')
			->withHeader('Access-Control-Allow-Methods', implode(', ', [
				'GET',
				'POST',
				'PUT',
				'DELETE',
				'OPTIONS',
			]))->withHeader('Access-Control-Allow-Credentials', 'true')
			->withHeader(
				'Access-Control-Allow-Headers',
				'DNT,Keep-Alive,User-Agent,Cache-Control,Content-Type,Authorization,X-Requested-With,Token',
			)
			->withHeader('Access-Control-Expose-Headers', 'Token')
			->withHeader('Server', 'Swoole');

		Context::set(ResponseInterface::class, $response);

		if (strcmp($request->getMethod(), 'OPTIONS') === 0) {
			return $response;
		}

		return $handler->handle($request);
	}
}