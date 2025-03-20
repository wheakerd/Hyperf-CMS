<?php
declare(strict_types=1);

namespace App\Security;

use App\Library\JsonWebToken\JWS;
use Hyperf\Config\Annotation\Value;

/**
 * @AdminSecurity
 * @\App\Security\AdminSecurity
 */
final class AdminSecurity extends JWS
{

	#[Value('jwk.admin')]
	protected string $key;
}