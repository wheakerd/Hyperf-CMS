<?php
declare(strict_types=1);

namespace App\Dao\Admin;

use App\Abstract\AbstractDao;
use App\Model\Admin\AdministratorModel;

/**
 * @AdministratorDao
 * @\App\Dao\Admin\AdministratorDao
 */
final readonly class AdministratorDao extends AbstractDao
{
	public function __construct(AdministratorModel $model)
	{
		parent::__construct($model);
	}

	/**
	 * 通过用户名称查询用户信息（取出一条数据）
	 *
	 * @param string $username
	 *
	 * @return AdministratorModel|null
	 */
	public function getUserinfoByUsername(string $username): ?AdministratorModel
	{
		return $this->newQuery->where('username', $username)->first();
	}
}