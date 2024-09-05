<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\DocumentMapper;

interface DocumentIdGeneratorInterface
{
    public function generateContentDocumentId(int $contentId, string $languageCode): string;

    public function generateLocationDocumentId(int $locationId, string $languageCode): string;
}

class_alias(DocumentIdGeneratorInterface::class, 'Ibexa\Platform\ElasticSearchEngine\DocumentMapper\DocumentIdGeneratorInterface');
