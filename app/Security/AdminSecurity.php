<?php
declare(strict_types=1);

namespace App\Security;

use App\Cache\Admin\SystemDefaultCache;
use App\Library\JsonWebToken\JWA;
use App\Library\JsonWebToken\JWT;
use Jose\Component\Core\JWK;

/**
 * @AdminSecurity
 * @\App\Security\AdminSecurity
 */
final readonly class AdminSecurity extends JWT
{
	public function __construct(SystemDefaultCache $systemDefaultCache, JWA $jsonWebAlgorithms)
	{
		$key                       = $systemDefaultCache->getAdminKey();
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