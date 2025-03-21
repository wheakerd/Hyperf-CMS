<?php
declare(strict_types=1);

namespace App\Schema\Admin;

use App\Interface\SchemaInterface;
use App\Model\Admin\ModelAdminAdministrator;
use Carbon\Carbon;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Schema\Schema;

/**
 * @AdminAdministratorSchema
 * @\App\Schema\AdminAdministratorSchema
 */
final readonly class AdminAdministratorSchema implements SchemaInterface
{
	public function __construct(private ModelAdminAdministrator $model)
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
			$blueprint->string('avatar', 100)->comment('头像');
			$blueprint->integer('create_time', false, true)->nullable(false)->comment('创建时间');
			$blueprint->integer('update_time', false, true)->nullable(false)->comment('更新时间');
			$blueprint->integer('delete_time', false, true)->comment('删除时间，软删除');
		});
		//  写入初始数据
		$this->model->newInstance(
			[
				'username'    => 'admin',
				'password'    => '123456',
				'role_id'     => true,
				'status'      => true,
				'avatar'      => null,
				'create_time' => Carbon::now()->getTimestamp(),
				'update_time' => Carbon::now()->getTimestamp(),
				'delete_time' => null,
			],
		)->save();
	}
}