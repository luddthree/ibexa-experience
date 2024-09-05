<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy\CDP\Export\Content\FieldValueProcessor\FieldType;

use Ibexa\Cdp\Test\Export\Content\FieldValueProcessor\AbstractFieldValueProcessorTest;
use Ibexa\Contracts\Cdp\Export\Content\FieldValueProcessorInterface;
use Ibexa\Contracts\Core\FieldType\Value as ValueInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Core\FieldType\FieldType;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Taxonomy\CDP\Export\Content\FieldValueProcessor\FieldType\TaxonomyEntryAssignmentFieldValueProcessor;
use Ibexa\Taxonomy\FieldType\TaxonomyEntryAssignment\Value;

final class TaxonomyEntryAssignmentFieldValueProcessorTest extends AbstractFieldValueProcessorTest
{
    public function createFieldValueProcessor(): FieldValueProcessorInterface
    {
        $fieldTypeMock = $this->createMock(FieldType::class);
        $fieldTypeMock
            ->method('getFieldTypeIdentifier')
            ->willReturn('ibexa_taxonomy_entry_assignment');

        return new TaxonomyEntryAssignmentFieldValueProcessor($fieldTypeMock, '[[%s]]');
    }

    public function providerForTestProcess(): iterable
    {
        $content = $this->createMock(Content::class);

        yield '1 entry' => [
            null,
            $this->createField(
                'tags',
                'ibexa_taxonomy_entry_assignment',
                new Value(
                    [
                        $this->createEntry(
                            1,
                            'car',
                            'Car',
                        ),
                    ],
                    'tags',
                ),
            ),
            $content,
            [
                'value_entries_id' => '[[1]]',
                'value_entries_identifier' => '[[car]]',
                'value_entries_name' => '[[Car]]',
                'value_taxonomy' => 'tags',
            ],
        ];

        yield 'multiple entries' => [
            null,
            $this->createField(
                'tags',
                'ibexa_taxonomy_entry_assignment',
                new Value(
                    [
                        $this->createEntry(1, 'car', 'Car'),
                        $this->createEntry(2, 'computer', 'Computer'),
                        $this->createEntry(3, 'phone', 'Phone'),
                        $this->createEntry(4, 'tablet', 'Tablet'),
                        $this->createEntry(5, 'keyboard', 'Keyboard'),
                        $this->createEntry(6, 'mouse', 'Mouse'),
                        $this->createEntry(7, 'lcd_screen', 'LCD Screen'),
                        $this->createEntry(8, 'mouse_pad', 'Mouse Pad'),
                        $this->createEntry(9, 'random_access_memory', 'Random Access Memory'),
                        $this->createEntry(10, 'floppy_drive', 'Floppy Drive'),
                    ],
                    'tags',
                ),
            ),
            $content,
            [
                'value_entries_id' => '[[1]], [[2]], [[3]], [[4]], [[5]], '
                    . '[[6]], [[7]], [[8]], [[9]], [[10]]',
                'value_entries_identifier' => '[[car]], [[computer]], [[phone]], [[tablet]], [[keyboard]], '
                    . '[[mouse]], [[lcd_screen]], [[mouse_pad]], [[random_access_memory]], [[floppy_drive]]',
                'value_entries_name' => '[[Car]], [[Computer]], [[Phone]], [[Tablet]], [[Keyboard]], '
                    . '[[Mouse]], [[LCD Screen]], [[Mouse Pad]], [[Random Access Memory]], [[Floppy Drive]]',
                'value_taxonomy' => 'tags',
            ],
        ];

        yield 'no entries' => [
            null,
            $this->createField(
                'tags',
                'ibexa_taxonomy_entry_assignment',
                new Value([], 'tags'),
            ),
            $content,
            [
                'value_entries_id' => '',
                'value_entries_identifier' => '',
                'value_entries_name' => '',
                'value_taxonomy' => 'tags',
            ],
        ];

        yield 'noninitialized value' => [
            null,
            $this->createField(
                'tags',
                'ibexa_taxonomy_entry_assignment',
                new Value(),
            ),
            $content,
            [
                'value_entries_id' => '',
                'value_entries_identifier' => '',
                'value_entries_name' => '',
                'value_taxonomy' => '',
            ],
        ];
    }

    public function providerForTestSupports(): iterable
    {
        $content = $this->createMock(Content::class);

        yield 'ibexa_taxonomy_entry_assignment' => [
            null,
            $this->createField(
                'tags',
                'ibexa_taxonomy_entry_assignment',
                $this->createMock(Value::class),
            ),
            $content,
            true,
        ];

        yield 'not supported generic Value' => [
            null,
            $this->createField(
                'incompatible',
                'incompatible',
                $this->createMock(ValueInterface::class),
            ),
            $content,
            false,
        ];
    }

    private function createEntry(
        int $id,
        string $identifier,
        string $name
    ): TaxonomyEntry {
        return new TaxonomyEntry(
            $id,
            $identifier,
            $name,
            'eng-GB',
            ['eng-GB' => $name],
            null,
            $this->createMock(Content::class),
            'tags',
            0,
        );
    }
}
