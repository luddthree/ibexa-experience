<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\Solr;

use Ibexa\Contracts\Core\Persistence\Content as SPIContent;
use Ibexa\Contracts\Solr\FieldMapper\ContentFieldMapper as BaseContentFieldMapper;
use Ibexa\ProductCatalog\Local\Repository\Search\Common\IndexDataProvider\IndexDataProviderInterface;

final class FieldTypeBlockFieldMapper extends BaseContentFieldMapper
{
    private IndexDataProviderInterface $indexDataProvider;

    public function __construct(IndexDataProviderInterface $indexDataProvider)
    {
        $this->indexDataProvider = $indexDataProvider;
    }

    public function accept(SPIContent $content): bool
    {
        return $this->indexDataProvider->isSupported($content);
    }

    public function mapFields(SPIContent $content): array
    {
        return $this->indexDataProvider->getSearchData($content);
    }
}
