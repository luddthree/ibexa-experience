<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Search\Solr;

use Ibexa\Contracts\Core\Persistence\Content as SPIContent;
use Ibexa\Contracts\Solr\FieldMapper\ContentFieldMapper as BaseContentFieldMapper;
use Ibexa\Taxonomy\Search\SearchIndexDataProvider;

final class TaxonomyEntryFieldMapper extends BaseContentFieldMapper
{
    private SearchIndexDataProvider $searchIndexDataProvider;

    public function __construct(SearchIndexDataProvider $searchIndexDataProvider)
    {
        $this->searchIndexDataProvider = $searchIndexDataProvider;
    }

    public function accept(SPIContent $content): bool
    {
        return true;
    }

    /**
     * @return \Ibexa\Contracts\Core\Search\Field[]
     */
    public function mapFields(SPIContent $content): array
    {
        return $this->searchIndexDataProvider->getSearchData($content);
    }
}
