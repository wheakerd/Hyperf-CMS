<?php
declare(strict_types=1);

namespace App\Abstract;

use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Model\Model;
use RuntimeException;

/**
 * @AbstractDao
 * @\App\Abstract\AbstractDao
 *
 * @property-read Model         $model         Get a model instance.
 * @property-read Builder       $newQuery      Get a new query builder for the model's table.
 * @property-read Model|Builder $newModelQuery Get a new query builder that doesn't have any global scopes or eager
 *                                             loading.
 */
abstract readonly class AbstractDao
{
	/**
	 * @param Model $model
	 */
	public function __construct(private Model $model)
	{
	}

	protected function newInstance(array $attributes = [], bool $exists = false): Model
	{
		return $this->model->newInstance($attributes, $exists);
	}

	public function __get(string $name)
	{
		return match ($name) {
			'model'         => $this->model,
			'newQuery'      => $this->model->newQuery(),
			'newModelQuery' => $this->model->newModelQuery(),
			default         => throw new RuntimeException("Property $name does not exist"),
		};
	}

//
//	/**
//	 * @return Model
//	 * @see AbstractModel
//	 */
//	protected function getModel(): Model
//	{
//		return $this->model;
//	}
//
//	/**
//	 * @return Builder
//	 * @see DbModel::newQuery
//	 */
//	public function newQuery(): Builder
//	{
//		return $this->model->newQuery();
//	}
//
//	/**
//	 * @return Model|Builder
//	 * @see DbModel::newModelQuery
//	 */
//	public function newModelQuery(): Model|Builder
//	{
//		return $this->model->newModelQuery();
//	}
//
//	public function newInstance(array $attributes = [], bool $exists = false): Model
//	{
//		return $this->model->newInstance($attributes, $exists);
//	}
//
//	/**
//	 * 通过 integer 主键更新单条数据
//	 *
//	 * @param int   $primaryKey
//	 * @param array $inputs
//	 *
//	 * @return boolean
//	 */
//	public function update(int $primaryKey, array $inputs): bool
//	{
//		/** @var null|Model $query */
//		$query = $this->newQuery()->find($primaryKey);
//
//		if (null === $query) {
//			throw new CustomMessageException('数据不存在，请刷新后重试！');
//		}
//
//		return $query->fill($inputs)->save();
//	}
//
//	public function count(string $column = '*', array $where = []): int
//	{
//		return $this->newQuery()->where($where)->count($column);
//	}
//
//	public function destroy(array|BaseCollection|int $ids): int
//	{
//		return $this->model::destroy($ids);
//	}
//
//	public function exist(array $where = []): bool
//	{
//		return $this->newQuery()->where($where)->exists();
//	}
//
//	public function find(array|int|string $id, array $columns = ['*']): Collection|DbModel|array|Builder|null
//	{
//		return $this->newQuery()->find($id, $columns);
//	}
//
//	public function insert(array $data): bool
//	{
//		return $this->newInstance($data)->save();
//	}
//
//	public function list(
//		string  $parentKey,
//		?string $search = null,
//		array   $sorts = [],
//		array   $with = [],
//		array   $where = [],
//		array   $columns = ['*'],
//	): array
//	{
//		$search = is_null($search) ? [] : (json_validate($search) ? json_decode($search, true) : []);
//
//		$key = $search[$parentKey] ?? null;
//
//		unset($search[$parentKey]);
//
//		$query = $this->newQuery()->where($where);
//
//		/** @var string[] $fillable */
//		$fillable = $this->model->getFillable();
//		$casts    = $this->model->getCasts();
//
//		foreach ($search as $field => $value) {
//			if (in_array($field, $fillable)) {
//				$field = Str::snake($field);
//				$type  = $casts[$field] ?? '';
//
//				if (in_array($type, [
//					'integer',
//					'string',
//				])) {
//					$query = $query->where($field, 'like', "%$value%");
//				}
//				if (str_starts_with($type, 'datetime')
//				    && is_array($value)
//				    && array_is_list($value)
//				    && 2 === count($value)
//				) {
//					[
//						$startTime,
//						$endTime,
//					] = $value;
//					$query = $query->whereBetween($field, [
//						$startTime,
//						$endTime,
//					]);
//				}
//				if ('boolean' === $type && is_bool($value)) {
//					$query = $query->where($field, $value);
//				}
//			}
//		}
//
//		$query = $query->with($with);
//
//		foreach ($sorts as $field => $direction) {
//			$query = is_int($field) ? $query->orderBy($field) : $query->orderBy($field, $direction);
//		}
//
//		$list = $query->select($columns)->get()->toArray();
//
//		return Functions::list($list, $this->model->getKeyName(), $parentKey, $key);
//	}
//
//	/**
//	 * @param string|null $search
//	 * @param array       $sorts
//	 * @param array       $with
//	 * @param array       $where
//	 * @param array       $columns
//	 *
//	 * @return array
//	 */
//	public function table(
//		?string $search = null,
//		array   $sorts = [],
//		array   $with = [],
//		array   $where = [],
//		array   $columns = ['*'],
//	): array
//	{
//		$search = is_null($search) ? [] : (json_validate($search) ? json_decode($search, true) : []);
//
//		$currentPage = $search['currentPage'] ?? 1;
//		$perPage     = $search['perPage'] ?? 20;
//
//		unset($search['currentPage'], $search['perPage']);
//
//		$query = $this->newQuery()->where($where);
//
//		/** @var string[] $fillable */
//		$fillable = $this->model->getFillable();
//		$casts    = $this->model->getCasts();
//
//		foreach ($search as $field => $value) {
//			if (in_array($field, $fillable)) {
//				$field = Str::snake($field);
//				$type  = $casts[$field] ?? '';
//
//				if (in_array($type, [
//					'integer',
//					'string',
//				])) {
//					$query = $query->where($field, 'like', "%$value%");
//				}
//				if (str_starts_with($type, 'datetime')
//				    && is_array($value)
//				    && array_is_list($value)
//				    && 2 === count($value)
//				) {
//					[
//						$startTime,
//						$endTime,
//					] = $value;
//					$query = $query->whereBetween($field, [
//						$startTime,
//						$endTime,
//					]);
//				}
//				if ('boolean' === $type && is_bool($value)) {
//					$query = $query->where($field, $value);
//				}
//			}
//		}
//
//		$query = $query->with($with);
//
//		foreach ($sorts as $field => $direction) {
//			$query = is_int($field) ? $query->orderBy($field) : $query->orderBy($field, $direction);
//		}
//
//		$total = $query->count();
//
//		$list = $query->offset(($currentPage - 1) * $perPage)->limit($perPage)->get($columns)->toArray();
//
//		return compact('total', 'list');
//	}
}