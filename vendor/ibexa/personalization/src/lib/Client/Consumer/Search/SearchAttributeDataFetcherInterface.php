<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Search;

use Psr\Http\Message\ResponseInterface;

/**
 * @internal
 */
interface SearchAttributeDataFetcherInterface
{
    /**
     * @param array<\Ibexa\Personalization\Value\Search\AttributeCriteria> $payload
     */
    public function search(int $customerId, string $licenseKey, array $payload): ResponseInterface;
}
