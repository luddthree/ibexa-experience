<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Connector\Dam\FieldType\ImageAsset;

use Ibexa\Contracts\Core\FieldType\Indexable;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition;
use Ibexa\Contracts\Core\Search;

/**
 * Indexable definition for ImageAsset field type.
 */
class SearchField implements Indexable
{
    /**
     * Get index data for field for the backend search.
     *
     * @param \Ibexa\Contracts\Core\Persistence\Content\Field $field
     * @param \Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition $fieldDefinition
     *
     * @return \Ibexa\Contracts\Core\Search\Field[]
     */
    public function getIndexData(Field $field, FieldDefinition $fieldDefinition)
    {
        return [
            new Search\Field(
                'id',
                $field->value->data['destinationContentId'] ?? null,
                new Search\FieldType\FullTextField()
            ),
            new Search\Field(
                'alternative_text',
                $field->value->data['alternativeText'] ?? null,
                new Search\FieldType\StringField()
            ),
            new Search\Field(
                'source',
                $field->value->data['source'] ?? null,
                new Search\FieldType\StringField()
            ),
        ];
    }

    /**
     * Get index field types for the backend search.
     *
     * @return \Ibexa\Contracts\Core\Search\FieldType[]
     */
    public function getIndexDefinition()
    {
        return [
            'id' => new Search\FieldType\StringField(),
        ];
    }

    /**
     * Get name of the default field to be used for matching.
     *
     * As field types can index multiple fields (see MapLocation field type's
     * implementation of this interface), this method is used to define default
     * field for matching. Default field is typically used by Field criterion.
     *
     * @return string
     */
    public function getDefaultMatchField()
    {
        return 'id';
    }

    /**
     * Get name of the default field to be used for sorting.
     *
     * As field types can index multiple fields (see MapLocation field type's
     * implementation of this interface), this method is used to define default
     * field for sorting. Default field is typically used by Field sort clause.
     *
     * @return string
     */
    public function getDefaultSortField()
    {
        return $this->getDefaultMatchField();
    }
}
