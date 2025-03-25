<?php
declare(strict_types=1);

namespace App\Schema\Admin;

use App\Interface\SchemaInterface;
use App\Model\Admin\AdministratorModel;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Schema\Schema;

/**
 * @AdministratorSchema
 * @\App\Schema\AdministratorSchema
 */
final readonly class AdministratorSchema implements SchemaInterface
{
	public function __construct(private AdministratorModel $model)
	{
	}

	/**
	 * @return void
	 */
	public function handle(): void
	{
		//  创建表结构
		Schema::create('admin_administrator', function (Blueprint $blueprint) {
			$blueprint->increments('id')->nullable(false)->comment('主键');
			$blueprint->string('username', 100)->nullable(false)->comment('用户名');
			$blueprint->string('password', 255)->nullable(false)->comment('密码（此字段数据较为特殊,请查阅加密算法文档）');
			$blueprint->unsignedInteger('role_id')->nullable(false)->comment('角色组ID，关联admin_role.id');
			$blueprint->addColumn(
				type      : 'tinyInteger',
				name      : 'status',
				parameters: [
					            'unsigned' => true,
					            'length'   => 1,
				            ],
			)->nullable(false)->comment('角色组ID，关联admin_role.id');
			$blueprint->string('avatar', 100)->nullable()->comment('头像');
			$blueprint->integer('create_time', false, true)->nullable(false)->comment('创建时间');
			$blueprint->integer('update_time', false, true)->nullable(false)->comment('更新时间');
			$blueprint->integer('delete_time', false, true)->nullable()->comment('删除时间，软删除');
		});
		//  写入初始数据
		$model = $this->model->newInstance();

		$model->username = 'admin';
		$model->password = '123456';
		$model->roleId   = 1;
		$model->status   = 1;
		$model->avatar   = null;

		$model->save();
	}
}