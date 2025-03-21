<?php
declare(strict_types=1);

namespace App\Controller\Guide;

use App\Abstract\AbstractHttpController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Psr\Http\Message\ResponseInterface;

/**
 * @IndexController
 * @\App\Controller\Guide\IndexController
 */
#[Controller(prefix: '/guide')]
final class IndexController extends AbstractHttpController
{
	/**
	 * 引导页面
	 *
	 * @return ResponseInterface
	 */
	#[RequestMapping(path: '', methods: 'GET')]
	public function index(): ResponseInterface
	{
		return $this->response->redirect('/web/index.html');
	}

	/**
	 * 前端资源
	 *
	 * @param string $path
	 *
	 * @return bool|ResponseInterface
	 */
	#[RequestMapping(path: '/guide/{path:.+}', methods: 'GET')]
	public function resource(string $path): bool|ResponseInterface
	{
		$filepath = ROOT_PATH . "/assets/guide/$path";

		if (file_exists($filepath) && is_file($filepath) && is_readable($filepath)) {
			return $this->response->getSocket()->sendfile($filepath);
		}

		return $this->response->raw('Not Found');
	}
}