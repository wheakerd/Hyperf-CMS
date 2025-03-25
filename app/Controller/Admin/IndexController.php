<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Abstract\AbstractHttpController;
use App\Middleware\Http\AdminAuthenticationMiddleware;
use App\Model\Admin\AdministratorModel;
use App\Service\Admin\ServiceAdminAdministrator;
use App\Validator\Admin\AdministratorValidator;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\Validation\Annotation\Scene;
use Psr\Http\Message\ResponseInterface;

/**
 * 零碎功能
 *
 * @IndexController
 * @\App\Controller\Admin\IndexController
 */
#[Controller(prefix: '/admin/index')]
final class IndexController extends AbstractHttpController
{
	public function __construct(private readonly ServiceAdminAdministrator $serviceAdminAdministrator)
	{
	}

	/**
	 * 登录
	 *
	 * @param AdministratorValidator $loginValidator
	 *
	 * @return ResponseInterface
	 *
	 * @api /admin/index/login
	 */
	#[
		RequestMapping(path: 'login', methods: ['POST']),
		Scene(scene: 'login', argument: 'loginValidator'),
	]
	public function login(AdministratorValidator $loginValidator): ResponseInterface
	{
		$inputs = $loginValidator->validated();

		/**
		 * @var string             $token
		 * @var AdministratorModel $userinfo
		 */
		[
			$token,
			$userinfo,
		] = $this->serviceAdminAdministrator->login(... $inputs);

		return $this->response->success(
			data   : $userinfo,
			headers: [
				         'Authorization' => $token,
			         ],
		);
	}

	/**
	 * 登出
	 *
	 * @return ResponseInterface
	 *
	 * @api /admin/index/logout
	 */
	#[
		RequestMapping(path: 'logout', methods: ['POST']),
		Middlewares([
			AdminAuthenticationMiddleware::class,
		]),
	]
	public function logout(): ResponseInterface
	{
		$token = $this->request->header('Authorization');

		//  移除签发凭证
		$this->serviceAdminAdministrator->logout($token);

		return $this->response->success();
	}
}