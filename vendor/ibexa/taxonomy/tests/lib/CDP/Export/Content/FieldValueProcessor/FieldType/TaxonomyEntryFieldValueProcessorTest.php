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
use Ibexa\Taxonomy\CDP\Export\Content\FieldValueProcessor\FieldType\TaxonomyEntryFieldValueProcessor;
use Ibexa\Taxonomy\FieldType\TaxonomyEntry\Value;

final class TaxonomyEntryFieldValueProcessorTest extends AbstractFieldValueProcessorTest
{
    public function createFieldValueProcessor(): FieldValueProcessorInterface
    {
        $fieldTypeMock = $this->createMock(FieldType::class);
        $fieldTypeMock
            ->method('getFieldTypeIdentifier')
            ->willReturn('ibexa_taxonomy_entry');

        return new TaxonomyEntryFieldValueProcessor($fieldTypeMock);
    }

    public function providerForTestProcess(): iterable
    {
        $content = $this->createMock(Content::class);

        yield '1 entry' => [
            null,
            $this->createField(
                'taxonomy_entry',
                'ibexa_taxonomy_entry',
                new Value(
                    new TaxonomyEntry(
                        1,
                        'car',
                        'Car',
                        'eng-GB',
                        ['eng-GB' => 'Car'],
                        null,
                        $this->createMock(Content::class),
                        'tags',
                        0,
                    )
                ),
            ),
            $content,
            [
                'value_entry_id' => 1,
                'value_entry_identifier' => 'car',
                'value_entry_name' => 'Car',
                'value_taxonomy' => 'tags',
            ],
        ];

        yield 'no entry' => [
            null,
            $this->createField(
                'taxonomy_entry',
                'ibexa_taxonomy_entry',
                new Value(null),
            ),
            $content,
            [
                'value_entry_id' => null,
                'value_entry_identifier' => null,
                'value_entry_name' => null,
                'value_taxonomy' => null,
            ],
        ];
    }

    public function providerForTestSupports(): iterable
    {
        $content = $this->createMock(Content::class);

        yield 'ibexa_taxonomy_entry' => [
            null,
            $this->createField(
                'taxonomy_entry',
                'ibexa_taxonomy_entry',
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
}
