<?php
declare(strict_types=1);

namespace App\Model\Admin;

use App\Abstract\AbstractModel;
use Carbon\Carbon;
use Hyperf\Database\Model\Relations\BelongsTo;
use Hyperf\Database\Model\Model;

/**
 * 管理员表
 *
 * @AdministratorModel
 * @\app\Model\Admin\AdministratorModel
 *
 * @property integer     $id            主键
 * @property string      $username      用户名
 * @property string      $password      密码（此字段数据较为特殊,请查阅加密算法文档）
 * @property integer     $roleId        角色组ID
 * @property boolean     $status        状态
 * @property string|null $avatar        头像
 * @property Carbon      $createTime    创建时间
 * @property Carbon      $updateTime    更新时间
 * @property integer     $deleteTime    软删除，删除时间
 */
final class AdministratorModel extends AbstractModel
{
	protected ?string $table = 'admin_administrator';

	protected array $fillable = [
		'id',
		'username',
		'password',
		'role_id',
		'status',
		'avatar',
		'create_time',
		'update_time',
		'delete_time',
	];

	protected array $casts = [
		'id'          => 'integer',
		'username'    => 'string',
		'password'    => 'string',
		'role_id'     => 'integer',
		'status'      => 'boolean',
		'avatar'      => 'string',
		'create_time' => 'datetime:Y-m-d H:i:s',
		'update_time' => 'datetime:Y-m-d H:i:s',
		'delete_time' => 'integer',
	];

	protected array $hidden = [
		'password',
	];

	/**
	 * @return BelongsTo
	 */
	public function roles(): BelongsTo
	{
		return $this->belongsTo(AdminRolesModel::class, 'role_id', 'id');
	}

	/**
	 * @param string $value
	 *
	 * @return void
	 * @used-by Model
	 */
	public function setPasswordAttribute(string $value): void
	{
		$this->attributes['password'] = password_hash($value, PASSWORD_DEFAULT);
	}
}