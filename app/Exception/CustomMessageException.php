<?php
declare(strict_types=1);

namespace App\Exception;

use Hyperf\Server\Exception\ServerException;

/**
 * Thrown a custom exception
 *
 * @CustomMessageException
 * @\App\Exception\CustomMessageException
 */
final class CustomMessageException extends ServerException
{
}