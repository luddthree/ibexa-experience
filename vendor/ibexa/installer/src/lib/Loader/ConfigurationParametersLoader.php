<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Installer\Loader;

use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;

/**
 * Loader of Commerce configuration from Search data.
 *
 * @internal
 */
final class ConfigurationParametersLoader
{
    /**
     * Map of parameter names for which content type ID should be fetched.
     */
    public const PARAM_CONTENT_TYPE_MAP = [
        'ibexa.commerce.site_access.config.core.default.product_catalog_content_type_id' => 'Product Catalog',
    ];

    /**
     * Map of parameter names for which main Location ID should be fetched.
     */
    public const PARAM_LOCATION_MAP = [
        'ibexa.commerce.site_access.config.core.default.user_group_location' => 'Shop users',
        'ibexa.commerce.site_access.config.core.default.user_group_location.business' => 'Business customers',
        'ibexa.commerce.site_access.config.core.default.user_group_location.private' => 'Private customers',
        'ibexa.commerce.site_access.config.tools.default.translationFolderId' => 'Textmodules',
    ];

    /** @var \Ibexa\Contracts\Core\Repository\Repository */
    private $repository;

    /** @var \Ibexa\Contracts\Core\Repository\SearchService */
    private $searchService;

    public function __construct(Repository $repository, SearchService $searchService)
    {
        $this->repository = $repository;
        $this->searchService = $searchService;
    }

    /**
     * @return iterable<string, ?int>
     *
     * @throws \Exception
     */
    public function load(): iterable
    {
        foreach (self::PARAM_CONTENT_TYPE_MAP as $key => $contentName) {
            $contentInfo = $this->findContentInfo($contentName);
            yield $key => null !== $contentInfo ? $contentInfo->contentTypeId : null;
        }

        foreach (self::PARAM_LOCATION_MAP as $key => $contentName) {
            $contentInfo = $this->findContentInfo($contentName);
            yield $key => null !== $contentInfo ? $contentInfo->mainLocationId : null;
        }
    }

    /**
     * @throws \Exception
     */
    private function findContentInfo(string $contentName): ?ContentInfo
    {
        $query = new Query();
        $criterion = [
            new Criterion\Field('name', Criterion\Operator::EQ, $contentName),
        ];
        $query->filter = new Criterion\LogicalAnd($criterion);

        $result = $this->repository->sudo(
            function () use ($query) {
                return $this->searchService->findContentInfo($query);
            }
        );

        return $result->totalCount > 0 ? $result->searchHits[0]->valueObject : null;
    }
}

class_alias(ConfigurationParametersLoader::class, 'Ibexa\Platform\Installer\Loader\ConfigurationParametersLoader');
