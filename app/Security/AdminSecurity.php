<?php
declare(strict_types=1);

namespace App\Security;

use App\Library\JsonWebToken\JsonWebAlgorithms;
use App\Library\JsonWebToken\JsonWebToken;
use Jose\Component\Core\JWK;

/**
 * @AdminSecurity
 * @\App\Security\AdminSecurity
 */
final readonly class AdminSecurity extends JsonWebToken
{
	public function __construct(JsonWebAlgorithms $jsonWebAlgorithms)
	{
		$key                       = '';
		$signatureAlgorithmManager = $jsonWebAlgorithms->create(['HS256']);
		$jwk                       = new JWK(
			[
				'kty' => 'oct',
				'k'   => $key,
			],
		);
		parent::__construct($signatureAlgorithmManager, $jwk);
	}
}