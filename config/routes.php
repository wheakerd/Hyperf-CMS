<?php
declare(strict_types=1);

use App\Contract\ResponseContract;
use Hyperf\HttpServer\Router\Router;
use Psr\Http\Message\ResponseInterface;

//  加载资源文件
Router::get('/public/{path:.+}', function (string $path, ResponseContract $response): ResponseInterface|bool {
	$filepath = ROOT_PATH . "/assets/public/$path";

	if (file_exists($filepath) && is_file($filepath) && is_readable($filepath)) {
		return $response->getSocket()->sendfile($filepath);
	}

	return $response->raw('Not Found');
});