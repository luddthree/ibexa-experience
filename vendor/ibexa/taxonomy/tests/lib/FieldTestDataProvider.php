<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy;

use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Core\FieldType\TextLine\Value as TextLineValue;
use Ibexa\Taxonomy\FieldType\TaxonomyEntry\Value as TaxonomyEntryValue;

final class FieldTestDataProvider
{
    /**
     * @return array<\Ibexa\Contracts\Core\Repository\Values\Content\Field>
     */
    public static function getFields(?TaxonomyEntry $parent = null): array
    {
        return [
            new Field([
                'fieldDefIdentifier' => 'identifier_ft',
                'value' => new TextLineValue('updated_example_entry'),
                'languageCode' => 'eng-GB',
                'fieldTypeIdentifier' => 'ezstring',
            ]),
            new Field([
                'fieldDefIdentifier' => 'parent_ft',
                'value' => new TaxonomyEntryValue($parent),
                'languageCode' => 'eng-GB',
                'fieldTypeIdentifier' => 'ibexa_taxonomy_entry',
            ]),
            new Field([
                'fieldDefIdentifier' => 'name_ft',
                'value' => new TextLineValue('Updated example entry'),
                'languageCode' => 'eng-GB',
                'fieldTypeIdentifier' => 'ezstring',
            ]),
        ];
    }
}
