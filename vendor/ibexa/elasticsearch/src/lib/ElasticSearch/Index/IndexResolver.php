<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\ElasticSearch\Index;

use Ibexa\Contracts\Elasticsearch\ElasticSearch\Index\Group\GroupResolverInterface;
use Ibexa\Contracts\ElasticSearch\Mapping\BaseDocument;

final class IndexResolver implements IndexResolverInterface
{
    /** @var \Ibexa\Contracts\Elasticsearch\ElasticSearch\Index\Group\GroupResolverInterface */
    private $groupResolver;

    /** @var string[] */
    private $prefixMap;

    public function __construct(GroupResolverInterface $resolver, array $prefixMap)
    {
        $this->groupResolver = $resolver;
        $this->prefixMap = $prefixMap;
    }

    public function getIndexWildcard(string $documentClass): string
    {
        return $this->prefixMap[$documentClass] . '_*';
    }

    public function getIndexNameForDocument(BaseDocument $document): string
    {
        $prefix = $this->prefixMap[get_class($document)];
        $group = $this->groupResolver->resolveDocumentGroup($document);

        return $prefix . '_' . $group;
    }
}

class_alias(IndexResolver::class, 'Ibexa\Platform\ElasticSearchEngine\ElasticSearch\Index\IndexResolver');
