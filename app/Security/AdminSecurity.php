<?php
declare(strict_types=1);

namespace App\Security;

/**
 * @AdminSecurity
 * @\App\Security\AdminSecurity
 */
final class AdminSecurity
{
	public function __construct(private string $key)
	{
	}

	/**
	 * 校验令牌凭证
	 *
	 * @return void
	 */
	public function check(string $token)
	{
	}
}