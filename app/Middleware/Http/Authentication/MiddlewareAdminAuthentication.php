<?php
declare(strict_types=1);

namespace App\Middleware\Http\Authentication;

use App\Contract\ResponseContract;
use App\Service\Admin\ServiceAdminAdministrator;
use Hyperf\Context\Context;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @MiddlewareAdminAuthentication
 * @\App\Middleware\Http\Authentication\MiddlewareAdminAuthentication
 */
final class MiddlewareAdminAuthentication implements MiddlewareInterface
{
	#[Inject]
	protected ResponseContract $response;

	#[Inject]
	protected ServiceAdminAdministrator $serviceAdminAdministrator;

	/**
	 * @param ServerRequestInterface  $request
	 * @param RequestHandlerInterface $handler
	 *
	 * @return ResponseInterface
	 */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$token = $request->getHeaderLine('token');

		$administrator = $this->serviceAdminAdministrator->getAdministratorByToken($token);

		if (is_null($administrator)) {
			return $this->response->auth('未登录或者登录已过期！');
		}

		if ($administrator === false) {
			return $this->response->auth('该账户已被封禁！');
		}

		/* @var null|bool $role */
		$roleStatus = $administrator->roles?->status;

		if (true !== $roleStatus) {
			return $this->response->auth('该账户所属角色已被封禁！');
		}

		if (false === $administrator->status) {
			return $this->response->auth('该账户已被封禁！');
		}

		Context::set('userinfo', $administrator);

		return $handler->handle($request);
	}
}