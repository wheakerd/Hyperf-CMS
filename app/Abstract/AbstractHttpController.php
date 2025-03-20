<?php
declare(strict_types=1);

namespace App\Abstract;

use App\Contract\ResponseContract;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Psr\Container\ContainerInterface;

/**
 * @AbstractHttpController
 * @\App\Controller\AbstractHttpController
 */
abstract class AbstractHttpController
{
	#[Inject]
	protected ContainerInterface $container;

	#[Inject]
	protected RequestInterface $request;

	#[Inject]
	protected ResponseContract $response;

	#[Inject]
	protected ValidatorFactoryInterface $validatorFactory;
}
