<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\ElasticSearch\Index;

use Ibexa\Contracts\Core\Persistence\Content\Type\Handler as ContentTypeHandler;
use Ibexa\Contracts\Core\Search\FieldType;
use Ibexa\Core\Search\Common\FieldRegistry;

final class FieldTypeResolver
{
    /** @var \Ibexa\Contracts\Core\Persistence\Content\Type\Handler */
    private $contentTypeHandler;

    /** @var \Ibexa\Core\Search\Common\FieldRegistry */
    private $indexableRegistry;

    /** @var array */
    private $mappings;

    public function __construct(
        ContentTypeHandler $contentTypeHandler,
        FieldRegistry $indexableRegistry,
        array $mappings
    ) {
        $this->contentTypeHandler = $contentTypeHandler;
        $this->indexableRegistry = $indexableRegistry;
        $this->mappings = $mappings;
    }

    public function getSortFieldType(
        string $contentTypeIdentifier,
        string $fieldDefinitionIdentifier,
        ?string $name = null
    ): ?string {
        $fieldTypeIdentifier = $this->getFieldTypeIdentifier($contentTypeIdentifier, $fieldDefinitionIdentifier);
        if ($fieldTypeIdentifier === null) {
            return null;
        }

        $indexable = $this->indexableRegistry->getType($fieldTypeIdentifier);
        if ($name === null) {
            $name = $indexable->getDefaultSortField();
        }

        foreach ($indexable->getIndexDefinition() as $indexFieldName => $indexFieldType) {
            if ($indexFieldName === $name) {
                return $this->getElasticDataType($indexFieldType);
            }
        }

        return null;
    }

    private function getElasticDataType(FieldType $inedxFieldType): string
    {
        return $this->mappings[$inedxFieldType->type]['type'];
    }

    private function getFieldTypeIdentifier(string $contentTypeIdentifier, string $fieldDefinitionIdentifier): ?string
    {
        $searchableFieldMap = $this->contentTypeHandler->getSearchableFieldMap();

        $searchableField = $searchableFieldMap[$contentTypeIdentifier][$fieldDefinitionIdentifier];
        if ($searchableField !== null) {
            return $searchableField['field_type_identifier'] ?? null;
        }

        return null;
    }
}

class_alias(FieldTypeResolver::class, 'Ibexa\Platform\ElasticSearchEngine\ElasticSearch\Index\FieldTypeResolver');
