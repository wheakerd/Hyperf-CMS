<?php
declare(strict_types=1);

namespace App\Model\Admin;

use App\Abstract\AbstractModel;
use Carbon\Carbon;

/**
 * 角色表
 *
 * @AdminRolesModel
 * @\app\Model\Admin\AdminRolesModel
 *
 * @property integer $id         主键
 * @property integer $parentId   上级角色组ID
 * @property string  $name       角色名称
 * @property array   $router     路由权限
 * @property boolean $status     状态
 * @property Carbon  $createTime 创建时间
 * @property Carbon  $updateTime 更新时间
 * @property integer $deleteTime 软删除，删除时间
 */
final class AdminRolesModel extends AbstractModel
{
	protected ?string $table = 'admin_roles';

	protected array $fillable = [
		'id',
		'parent_id',
		'name',
		'router',
		'status',
		'create_time',
		'update_time',
		'delete_time',
	];

	protected array $casts = [
		'id'          => 'integer',
		'parent_id'   => 'integer',
		'name'        => 'string',
		'router'      => 'array',
		'status'      => 'boolean',
		'create_time' => 'datetime:Y-m-d H:i:s',
		'update_time' => 'datetime:Y-m-d H:i:s',
		'delete_time' => 'integer',
	];
}