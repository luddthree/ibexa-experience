<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Exceptions;

use Ibexa\Contracts\Core\Repository\Exceptions\Exception;
use RuntimeException;

final class ConfigurationException extends RuntimeException implements Exception
{
}
