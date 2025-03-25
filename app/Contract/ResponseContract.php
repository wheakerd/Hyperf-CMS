<?php
declare(strict_types=1);

namespace App\Contract;

use Hyperf\Codec\Json;
use Hyperf\HttpServer\Exception\Http\EncodingException;
use Hyperf\HttpServer\Response;
use Psr\Http\Message\ResponseInterface as Psr7ResponseInterface;
use Swoole\Http\Response as SwooleResponse;
use Swow\Psr7\Message\ResponsePlusInterface;
use Throwable;

/**
 * @ResponseContract
 * @\App\Contract\ResponseContract
 */
final class ResponseContract extends Response
{
	/**
	 * 默认消息发送
	 *
	 * @param string $message
	 * @param int    $code
	 *
	 * @return Psr7ResponseInterface
	 */
	public function message(string $message, int $code = 200): Psr7ResponseInterface
	{
		return $this->json(
			[
				'message' => $message,
			],
		)->withStatus($code);
	}

	/**
	 * @code 200
	 *
	 * @param array $data
	 */
	public function success(mixed $data = [], string $message = 'success', array $headers = []): Psr7ResponseInterface
	{
		$response = $this->json(
			[
				'message' => $message,
				'data'    => $data,
			],
		);

		foreach ($headers as $name => $value) {
			$response = $response->withHeader($name, $value);
		}

		return $response;
	}

	/**
	 * @param string $message
	 *
	 * @return Psr7ResponseInterface
	 */
	public function error(string $message = 'error'): Psr7ResponseInterface
	{
		return $this->message($message, 404);
	}

	/**
	 * @param string $message
	 *
	 * @return Psr7ResponseInterface
	 */
	public function validator(string $message = 'error'): Psr7ResponseInterface
	{
		return $this->message($message, 400);
	}

	public function auth(string $message = 'success'): Psr7ResponseInterface
	{
		return $this->message($message, 401);
	}

	public function server(string $message = 'System Error'): Psr7ResponseInterface
	{
		return $this->message($message, 500);
	}

	/**
	 * 返回创建好的事件流响应对象
	 *
	 * @return SwooleResponse
	 * @noinspection PhpUnused
	 */
	public function getEventStream(): SwooleResponse
	{
		$response = $this->getSocket();

		$response->header('Access-Control-Allow-Origin', '*');
		$response->header('Access-Control-Allow-Private-Network', 'true');
		$response->header('Access-Control-Allow-Credentials', 'true');
		$response->header('Content-Type', 'text/event-stream');
		$response->header('Cache-Control', 'no-store');
		$response->header('X-Accel-Buffering', 'no');

		return $response;
	}

	/**
	 * 获取 Swoole 响应对象
	 *
	 * @return SwooleResponse
	 */
	public function getSocket(): SwooleResponse
	{
		/* @var \Hyperf\HttpMessage\Server\Response $response */
		$response = $this->getResponse();
		/* @var SwooleResponse $socket */
		$socket = $response->getConnection()->getSocket();

		$socket->header = $this->getResponse()->getHeaders();

		return $socket;
	}

	public function toJson(mixed $data): string
	{
		try {
			$result = Json::encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		}
		catch (Throwable $exception) {
			throw new EncodingException($exception->getMessage(), (int)$exception->getCode(), $exception);
		}

		return $result;
	}

	protected function getResponse(): ResponsePlusInterface
	{
		return parent::getResponse()->withHeader('Server', 'Swoole');
	}
}