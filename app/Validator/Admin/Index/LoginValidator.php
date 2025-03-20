<?php
declare(strict_types=1);

namespace App\Validator\Admin\Index;


use Hyperf\Validation\Request\FormRequest;

/**
 * @LoginValidator
 * @\App\Validator\Admin\Index\LoginValidator
 */
final class LoginValidator extends FormRequest
{
	protected array $scenes = [
		'login' => [
			'username',
			'password',
		],
	];

	public function rules(): array
	{
		return [
			'username' => 'required|string|between:8,16',
			'password' => 'required|string|between:8,16',
		];
	}
}