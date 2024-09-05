<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\ElasticSearch\Index;

use Ibexa\Contracts\Elasticsearch\Mapping\BaseDocument;

interface IndexResolverInterface
{
    public function getIndexWildcard(string $documentClass): string;

    public function getIndexNameForDocument(BaseDocument $document): string;
}

class_alias(IndexResolverInterface::class, 'Ibexa\Platform\ElasticSearchEngine\ElasticSearch\Index\IndexResolverInterface');
