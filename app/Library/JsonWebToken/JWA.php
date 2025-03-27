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
 * 算法管理器工厂
 *
 * @JWA
 * @\App\Library\JWT\JWA
 */
final readonly class JWA
{
	/**
	 * @param AlgorithmManagerFactory $algorithmManagerFactory 算法管理器工厂
	 *
	 * @noinspection SpellCheckingInspection
	 */
	public function __construct(private AlgorithmManagerFactory $algorithmManagerFactory)
	{
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
	 * @param string[] $algorithms
	 *
	 * @return AlgorithmManager
	 */
	public function create(array $algorithms): AlgorithmManager
	{
		return $this->algorithmManagerFactory->create($algorithms);
	}
}