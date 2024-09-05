<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Customer;

use Psr\Http\Message\ResponseInterface;

interface InformationDataFetcherInterface
{
    public function fetchInformation(
        int $customerId,
        string $licenseKey,
        ?array $queryParams = null
    ): ResponseInterface;
}

class_alias(InformationDataFetcherInterface::class, 'Ibexa\Platform\Personalization\Client\Consumer\Customer\InformationDataFetcherInterface');
