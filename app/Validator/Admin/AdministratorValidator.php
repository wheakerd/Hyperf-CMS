<?php
declare(strict_types=1);

namespace App\Validator\Admin;


use Hyperf\Validation\Request\FormRequest;

/**
 * @AdministratorValidator
 * @\App\Validator\Admin\AdministratorValidator
 */
final class AdministratorValidator extends FormRequest
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