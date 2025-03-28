<?php
declare(strict_types=1);

namespace App\Abstract;

use App\Exception\CustomMessageException;
use App\Utils\Functions;
use Closure;
use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Model\Model;
use Hyperf\Stringable\Str;
use RuntimeException;

/**
 * @AbstractDao
 * @\App\Abstract\AbstractDao
 *
 * @property-read Model         $model            Get a model instance.
 * @property-read Model         $newInstance      Create a new instance of the given model.
 * @property-read Builder       $newQuery         Get a new query builder for the model's table.
 * @property-read Model|Builder $newModelQuery    Get a new query builder that doesn't have any global scopes or eager
 *                                                loading.
 */
abstract readonly class AbstractDao
{
	/**
	 * @param Model $model
	 */
	public function __construct(private Model $model)
	{
	}

	public function __get(string $name): Builder|Model
	{
		return match ($name) {
			'model'         => $this->model,
			'newQuery'      => $this->model->newQuery(),
			'newInstance'   => $this->model->newInstance(),
			'newModelQuery' => $this->model->newModelQuery(),
			default         => throw new RuntimeException("Property $name does not exist"),
		};
	}

	/**
	 * 删除
	 *
	 * @param array|callable $where
	 * @param bool           $force 是否强制删除
	 *
	 * @return bool
	 */
	final public function delete(array|callable $where, bool $force = false): bool
	{
		if ($where instanceof Closure) {
			$where($query = $this->newQuery);
		} else {
			$query = $this->newQuery->where($where);
		}

		return boolval($force ? $query->forceDelete() : $query->delete());
	}

	/**
	 * 列表（也可作用于多级菜单、搜索菜单等）
	 *
	 * @param string        $parentKey
	 * @param string|null   $params 搜索项的json字符串
	 * @param int           $currentPage
	 * @param int           $perPage
	 * @param array         $with
	 * @param array         $sorts
	 * @param callable|null $where
	 * @param array         $columns
	 *
	 * @return array
	 */
	final public function list(
		string    $parentKey,
		?string   $params = null,
		int       $currentPage = 1,
		int       $perPage = 20,
		array     $with = [],
		array     $sorts = [],
		?callable $where = null,
		array     $columns = ['*'],
	): array
	{
		['list' => $list] = $this->table(...func_get_args());

		$key = min(array_unique(array_column($list, $parentKey)));

		return Functions::list($list, $this->model->getKeyName(), $parentKey, $key);
	}

	/**
	 * 保存
	 *
	 * @param array $inputs
	 *
	 * @return bool
	 */
	final public function save(array &$inputs): bool
	{
		$primaryKey = $this->model->getKeyName();

		$id = $inputs[$primaryKey] ?? null;
		unset($inputs[$primaryKey]);

		if (is_null($id)) {
			return $this->newInstance->fillable($inputs)->save();
		}

		$query = $this->newQuery->find($inputs[$primaryKey]);

		if (is_null($query)) {
			throw new CustomMessageException('数据不存在，请刷新后重试！');
		}

		return $query->fill($inputs)->save();
	}

	/**
	 * 列表（仅作用于一级菜单）
	 *
	 * @param string|null   $params
	 * @param int           $currentPage
	 * @param int           $perPage
	 * @param array         $with
	 * @param array         $sorts
	 * @param callable|null $where
	 * @param array         $columns
	 *
	 * @return array
	 */
	final public function select(
		?string   $params = null,
		int       $currentPage = 1,
		int       $perPage = 20,
		array     $with = [],
		array     $sorts = [],
		?callable $where = null,
		array     $columns = ['*'],
	): array
	{
		['list' => $list] = $this->table(...func_get_args());

		return $list;
	}

	/**
	 * 表格
	 *
	 * @param string|null   $params
	 * @param int           $currentPage
	 * @param int           $perPage
	 * @param array         $sorts
	 * @param array         $with
	 * @param callable|null $where
	 * @param array         $columns
	 *
	 * @return array
	 */
	final public function table(
		?string   $params = null,
		int       $currentPage = 1,
		int       $perPage = 20,
		array     $sorts = [],
		array     $with = [],
		?callable $where = null,
		array     $columns = ['*'],
	): array
	{
		$search = $params ? json_decode($params, true) : [];

		if ($where instanceof Closure) {
			$where($query = $this->newQuery);
		} else {
			$query = $this->newQuery->where($where);
		}

		/** @var string[] $fillable */
		$fillable = $this->model->getFillable();
		$casts    = $this->model->getCasts();

		foreach ($search as $field => $value) {
			if (!in_array($field, $fillable)) continue;

			$field = Str::snake($field);
			if (!in_array($field, $casts)) continue;

			$type = $casts[$field];
			$type = strpos($type, ':') ? strstr($type, ':', true) : $type;

			$query = match ($type) {
				'boolean',
				'integer'  => $query->where($field, $value),
				'string'   => $query->where($field, 'like', "%$value%"),
				'datetime' => $query->whereBetween($field, $value),
				default    => $query,
			};
		}

		$query = $query->with($with);

		foreach ($sorts as $field => $direction) {
			$query = is_int($field) ? $query->orderBy($field) : $query->orderBy($field, $direction);
		}

		$total = $query->count();
		$list  = $query->offset(($currentPage - 1) * $perPage)->limit($perPage)->get($columns)->toArray();

		return compact('total', 'list');
	}
}