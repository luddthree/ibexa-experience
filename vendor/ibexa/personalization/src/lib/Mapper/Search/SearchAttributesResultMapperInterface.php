<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Mapper\Search;

/**
 * @internal
 */
interface SearchAttributesResultMapperInterface
{
    /**
     * @phpstan-param array<array{
     *  'itemType': string,
     *  'externalId': string,
     *  'attributes': array<array{
     *      'name': string,
     *      'value': string
     *  }>
     * }> $searchResult
     *
     * @return array<\Ibexa\Personalization\Value\Search\SearchHit>
     */
    public function map(int $customerId, array $searchResult): array;
}
