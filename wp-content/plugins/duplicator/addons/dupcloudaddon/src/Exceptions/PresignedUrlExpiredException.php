<?php

declare(strict_types=1);

namespace Duplicator\Addons\DupCloudAddon\Exceptions;

use Exception;

/**
 * Exception thrown when a presigned URL has expired
 */
class PresignedUrlExpiredException extends Exception
{
}
