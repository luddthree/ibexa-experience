<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\ElasticSearch\Index\Group;

use Ibexa\Contracts\Elasticsearch\ElasticSearch\Index\Group\GroupResolverInterface;
use Ibexa\Contracts\Elasticsearch\Mapping\BaseDocument;

final class ContentTypeGroupResolver implements GroupResolverInterface
{
    private const NO_LIMIT = 0;

    /** @var int */
    private $limit;

    public function __construct(int $limit = self::NO_LIMIT)
    {
        $this->limit = $limit;
    }

    public function resolveDocumentGroup(BaseDocument $document): string
    {
        $index = $document->contentTypeId;
        if ($this->limit > self::NO_LIMIT) {
            $index %= $this->limit;
        }

        return (string)$index;
    }
}
