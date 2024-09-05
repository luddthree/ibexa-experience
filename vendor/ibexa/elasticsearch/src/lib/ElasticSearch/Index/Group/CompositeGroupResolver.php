<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\ElasticSearch\Index\Group;

use Ibexa\Contracts\Elasticsearch\ElasticSearch\Index\Group\GroupResolverInterface;
use Ibexa\Contracts\Elasticsearch\Mapping\BaseDocument;

final class CompositeGroupResolver implements GroupResolverInterface
{
    private const SEGMENT_SEPARATOR = '_';

    /** @var iterable<\Ibexa\Contracts\Elasticsearch\ElasticSearch\Index\Group\GroupResolverInterface> */
    private $resolvers;

    /**
     * @param iterable<\Ibexa\Contracts\Elasticsearch\ElasticSearch\Index\Group\GroupResolverInterface> $resolvers
     */
    public function __construct(iterable $resolvers)
    {
        $this->resolvers = $resolvers;
    }

    public function resolveDocumentGroup(BaseDocument $document): string
    {
        $groups = [];
        foreach ($this->resolvers as $resolver) {
            $groups[] = $resolver->resolveDocumentGroup($document);
        }

        return implode(self::SEGMENT_SEPARATOR, $groups);
    }
}
