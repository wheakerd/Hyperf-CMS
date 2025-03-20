<?php
declare(strict_types=1);

namespace App\Library\JsonWebToken;

use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Core\AlgorithmManagerFactory;
use Jose\Component\Encryption\Algorithm\ContentEncryption\A128CBCHS256;
use Jose\Component\Encryption\Algorithm\KeyEncryption\A256KW;
use Jose\Component\Encryption\Algorithm\KeyEncryption\PBES2HS512A256KW;
use Jose\Component\Signature\Algorithm\HS256;
use Jose\Component\Signature\Algorithm\PS256;

/**
 * 组件 算法管理器工厂
 *
 * @JsonWebAlgorithms
 * @\App\Library\JsonWebToken\JsonWebAlgorithms
 */
final class JsonWebAlgorithms
{
	/**
	 * 当前类的实例
	 *
	 * @var self $JWA
	 */
	static private self $JWA;

	/**
	 * 算法管理器工厂
	 *
	 * @var AlgorithmManagerFactory $algorithmManagerFactory
	 */
	protected AlgorithmManagerFactory $algorithmManagerFactory;

	/**
	 * 禁止外部实例化
	 *
	 * @noinspection SpellCheckingInspection
	 */
	private function __construct()
	{
		$this->algorithmManagerFactory = new AlgorithmManagerFactory();
		$this->algorithmManagerFactory->add('A128CBC-HS256', new A128CBCHS256());
		$this->algorithmManagerFactory->add('A256KW', new A256KW());
		$this->algorithmManagerFactory->add('HS256', new HS256());
		$this->algorithmManagerFactory->add('PS256', new PS256());
		$this->algorithmManagerFactory->add('PBES2-HS512+A256KW', new PBES2HS512A256KW());
		$this->algorithmManagerFactory->add(
			'PBES2-HS512+A256KW with custom configuration',
			new PBES2HS512A256KW(128, 8192),
		);
	}

	/**
	 * 创造者
	 *
	 * @param array $algorithms
	 *
	 * @return AlgorithmManager
	 */
	static public function creator(array $algorithms): AlgorithmManager
	{
		if (!isset(self::$JWA)) {
			self::$JWA = new self;
		}

		return self::$JWA->algorithmManagerFactory->create($algorithms);
	}

	/**
	 * @return JWA
	 */
	private function __clone()
	{
		return self::$JWA;
	}

}