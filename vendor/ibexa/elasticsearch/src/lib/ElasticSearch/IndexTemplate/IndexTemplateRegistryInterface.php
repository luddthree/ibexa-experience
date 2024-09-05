<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Elasticsearch\ElasticSearch\IndexTemplate;

use Iterator;

interface IndexTemplateRegistryInterface
{
    public function getIndexTemplate(string $name): array;

    public function hasIndexTemplate(string $name): bool;

    public function getIterator(): Iterator;
}

class_alias(IndexTemplateRegistryInterface::class, 'Ibexa\Platform\ElasticSearchEngine\ElasticSearch\IndexTemplate\IndexTemplateRegistryInterface');
