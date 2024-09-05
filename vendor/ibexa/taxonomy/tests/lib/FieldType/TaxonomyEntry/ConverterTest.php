<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy\FieldType\TaxonomyEntry;

use Ibexa\Contracts\Core\Persistence\Content\FieldTypeConstraints;
use Ibexa\Contracts\Core\Persistence\Content\FieldValue;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition;
use Ibexa\Core\FieldType\FieldSettings;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldDefinition;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldValue;
use Ibexa\Taxonomy\FieldType\TaxonomyEntry\Converter;
use PHPUnit\Framework\TestCase;

final class ConverterTest extends TestCase
{
    private Converter $converter;

    protected function setUp(): void
    {
        $this->converter = new Converter();
    }

    public function testGetIndexColumn(): void
    {
        self::assertFalse($this->converter->getIndexColumn());
    }

    public function testToStorageValue(): void
    {
        $fieldValue = new FieldValue([
            'data' => [
                'taxonomy_entry' => 1,
            ],
        ]);
        $storageFieldValue = new StorageFieldValue();

        $this->converter->toStorageValue($fieldValue, $storageFieldValue);

        self::assertEquals(1, $storageFieldValue->dataInt);
    }

    public function testToFieldValue(): void
    {
        $fieldValue = new FieldValue();
        $storageFieldValue = new StorageFieldValue([
            'dataInt' => 1,
        ]);

        $this->converter->toFieldValue($storageFieldValue, $fieldValue);

        self::assertIsArray($fieldValue->data);
        self::assertArrayHasKey('taxonomy_entry', $fieldValue->data);
        self::assertEquals(1, $fieldValue->data['taxonomy_entry']);
    }

    public function testToStorageFieldDefinition(): void
    {
        $fieldDefinition = new FieldDefinition([
            'fieldTypeConstraints' => new FieldTypeConstraints([
                'fieldSettings' => ['taxonomy' => 'foobar'],
            ]),
        ]);
        $storageFieldDefinition = new StorageFieldDefinition();

        $this->converter->toStorageFieldDefinition($fieldDefinition, $storageFieldDefinition);

        self::assertEquals('foobar', $storageFieldDefinition->dataText1);
    }

    public function testToFieldDefinition(): void
    {
        $fieldDefinition = new FieldDefinition();
        $storageFieldDefinition = new StorageFieldDefinition([
            'dataText1' => 'foobar',
        ]);

        $this->converter->toFieldDefinition($storageFieldDefinition, $fieldDefinition);

        self::assertEquals(
            new FieldSettings(['taxonomy' => 'foobar']),
            $fieldDefinition->fieldTypeConstraints->fieldSettings,
        );
    }
}
