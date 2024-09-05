<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Exception;

use Ibexa\Contracts\Core\Repository\Exceptions\Exception as RepositoryException;
use RuntimeException;

/**
 * Thrown when method has been invoked at an illegal or inappropriate time e.g. method was called before proper object
 * initialization.
 */
final class IllegalStateException extends RuntimeException implements RepositoryException
{
}
