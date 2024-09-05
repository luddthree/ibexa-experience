<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Import;

use Psr\Http\Message\ResponseInterface;

interface ImportDataFetcherInterface
{
    public function fetchImportedItems(
        int $customerId,
        string $licenseKey
    ): ResponseInterface;
}

class_alias(ImportDataFetcherInterface::class, 'Ibexa\Platform\Personalization\Client\Consumer\Import\ImportDataFetcherInterface');
