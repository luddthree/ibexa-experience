<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;

trait MigrationTestAssertionsTrait
{
    protected static function assertContentTypeExists(string $identifier): void
    {
        try {
            $contentTypeService = self::getContentTypeService();
            $contentTypeService->loadContentTypeByIdentifier($identifier);
            $found = true;
        } catch (NotFoundException $e) {
            $found = false;
        }

        self::assertTrue($found, sprintf('ContentType with identifier "%s" does not exist.', $identifier));
    }
}
