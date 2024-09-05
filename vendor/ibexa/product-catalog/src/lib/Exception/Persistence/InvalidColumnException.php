<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Exception\Persistence;

use InvalidArgumentException;

final class InvalidColumnException extends InvalidArgumentException
{
    /**
     * @param string[] $missingColumns
     * @param string[] $availableColumns
     */
    public function __construct(string $tableName, array $missingColumns, array $availableColumns)
    {
        $message = sprintf(
            '"%s" do not exist in %s. Available columns are: "%s"',
            implode('", "', $missingColumns),
            $tableName,
            implode('", "', $availableColumns),
        );

        parent::__construct($message);
    }
}
