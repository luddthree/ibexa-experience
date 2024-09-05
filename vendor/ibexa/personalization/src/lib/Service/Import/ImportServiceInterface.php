<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Service\Import;

use Ibexa\Personalization\Value\Import\ImportedItemList;

/**
 * @internal
 */
interface ImportServiceInterface
{
    public function getImportedItems(int $customerId): ImportedItemList;
}

class_alias(ImportServiceInterface::class, 'Ibexa\Platform\Personalization\Service\Import\ImportServiceInterface');
