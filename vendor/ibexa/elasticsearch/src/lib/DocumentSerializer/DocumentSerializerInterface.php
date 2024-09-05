<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\DocumentSerializer;

use Ibexa\Contracts\Core\Search\Document;

interface DocumentSerializerInterface
{
    public function serialize(Document $document): array;
}

class_alias(DocumentSerializerInterface::class, 'Ibexa\Platform\ElasticSearchEngine\DocumentSerializer\DocumentSerializerInterface');
