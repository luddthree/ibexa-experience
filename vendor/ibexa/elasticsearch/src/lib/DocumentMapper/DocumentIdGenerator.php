<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\DocumentMapper;

final class DocumentIdGenerator implements DocumentIdGeneratorInterface
{
    public function generateContentDocumentId(int $contentId, string $languageCode): string
    {
        return sprintf('content-%d-%s', $contentId, $languageCode);
    }

    public function generateLocationDocumentId(int $locationId, string $languageCode): string
    {
        return sprintf('location-%d-%s', $locationId, $languageCode);
    }
}

class_alias(DocumentIdGenerator::class, 'Ibexa\Platform\ElasticSearchEngine\DocumentMapper\DocumentIdGenerator');
