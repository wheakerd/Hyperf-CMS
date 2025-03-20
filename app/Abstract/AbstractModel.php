<?php /** @formatter:off */
declare(strict_types=1);

namespace App\Abstract;

use Hyperf\Database\Model\Concerns\CamelCase;
use Hyperf\Database\Model\SoftDeletes;
use Hyperf\DbConnection\Model\Model;

/**
 * @ModelAbstract
 * @\App\Abstract\ModelAbstract
 */
abstract class AbstractModel extends Model
{
    use CamelCase, SoftDeletes;

    public bool $timestamps = true;
    protected ?string $dateFormat = 'U';
    public const string CREATED_AT = 'create_time';
    public const string UPDATED_AT = 'update_time';
    public const string DELETED_AT = 'delete_time';
}