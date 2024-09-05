<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\CatalogFilter\DefinitionProvider\Exception;

use Exception;
use Ibexa\Contracts\Core\Exception\InvalidArgumentException;

final class UnknownFilterDefinitionException extends InvalidArgumentException
{
    public function __construct(string $identifier, Exception $previous = null)
    {
        parent::__construct(
            '$identifier',
            sprintf('Unknown filter definition with identifier "%s"', $identifier),
            $previous
        );
    }
}
