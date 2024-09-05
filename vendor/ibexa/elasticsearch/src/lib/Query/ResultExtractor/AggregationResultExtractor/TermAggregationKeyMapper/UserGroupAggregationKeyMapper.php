<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor\TermAggregationKeyMapper;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor\TermAggregationKeyMapper;

final class UserGroupAggregationKeyMapper implements TermAggregationKeyMapper
{
    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\UserMetadataTermAggregation $aggregation
     * @param string[] $keys
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\UserGroup[]
     */
    public function map(Aggregation $aggregation, LanguageFilter $languageFilter, array $keys): array
    {
        $results = [];
        foreach ($keys as $key) {
            try {
                $results[$key] = $this->userService->loadUserGroup((int)$key);
            } catch (NotFoundException | UnauthorizedException $e) {
                // Skip missing user groups
            }
        }

        return $results;
    }
}

class_alias(UserGroupAggregationKeyMapper::class, 'Ibexa\Platform\ElasticSearchEngine\Query\ResultExtractor\AggregationResultExtractor\TermAggregationKeyMapper\UserGroupAggregationKeyMapper');
