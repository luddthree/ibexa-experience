<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\CDP\Export\Content\FieldValueProcessor\FieldType;

use Ibexa\Contracts\Cdp\Export\Content\FieldValueProcessorInterface;
use Ibexa\Contracts\Core\FieldType\FieldType;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;

final class TaxonomyEntryFieldValueProcessor implements FieldValueProcessorInterface
{
    private FieldType $fieldType;

    public function __construct(FieldType $fieldType)
    {
        $this->fieldType = $fieldType;
    }

    public function process(Field $field, Content $content): array
    {
        /** @var \Ibexa\Taxonomy\FieldType\TaxonomyEntry\Value $value */
        $value = $field->getValue();

        $data = [
            'value_entry_id' => null,
            'value_entry_identifier' => null,
            'value_entry_name' => null,
            'value_taxonomy' => null,
        ];

        $taxonomyEntry = $value->getTaxonomyEntry();

        if (null !== $taxonomyEntry) {
            $data = array_replace($data, [
                'value_entry_id' => $taxonomyEntry->getId(),
                'value_entry_identifier' => $taxonomyEntry->getIdentifier(),
                'value_entry_name' => $taxonomyEntry->getName(),
                'value_taxonomy' => $taxonomyEntry->getTaxonomy(),
            ]);
        }

        return $data;
    }

    public function supports(Field $field, Content $content): bool
    {
        return $this->fieldType->getFieldTypeIdentifier() === $field->fieldTypeIdentifier;
    }
}
