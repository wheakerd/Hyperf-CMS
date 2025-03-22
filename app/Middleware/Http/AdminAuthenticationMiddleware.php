<?php
declare(strict_types=1);

namespace App\Middleware\Http;

use App\Cache\Admin\AdminAdministratorCache;
use App\Contract\ResponseContract;
use App\Model\Admin\AdminRolesModel;
use App\Service\Admin\ServiceAdminAdministrator;
use Hyperf\Context\Context;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @AdminAuthenticationMiddleware
 * @\App\Middleware\Http\AdminAuthenticationMiddleware
 */
final readonly class AdminAuthenticationMiddleware implements MiddlewareInterface
{
	public function __construct(
		private ResponseContract          $response,
		private AdminAdministratorCache   $adminAdministratorCache,
		private ServiceAdminAdministrator $serviceAdminAdministrator,
	)
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
		$token = $request->getHeaderLine('Authorization');
		if (!strlen($token)) {
			return $this->response->auth('用户未登录！');
		}

		$administratorId = $this->adminAdministratorCache->hasToken($token);
		if (false === $administratorId) {
			return $this->response->auth('身份异常，请重新登录后重试！');
		}

		$userinfo = $this->serviceAdminAdministrator->getAdministratorInfoById($administratorId);

		if (null === $userinfo) {
			return $this->response->auth('该账户已被删除！');
		}
		if (false === $userinfo->status) {
			return $this->response->auth('该账户已被封禁！');
		}

		/* @var AdminRolesModel|null $role */
		$role = $userinfo->roles()->first();

		if (true !== $role?->status) {
			return $this->response->auth('该账户所属角色已被封禁！');
		}

		if (false === $userinfo->status) {
			return $this->response->auth('该账户已被封禁！');
		}

		Context::set('userinfo', $userinfo);

		return $handler->handle($request);
	}
}