<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Seo\Resolver;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Core\FieldType;
use Ibexa\Seo\Resolver\FieldReferenceResolver;
use PHPUnit\Framework\TestCase;

final class FieldReferenceResolverTest extends TestCase
{
    private FieldReferenceResolver $fieldReference;

    private Content $content;

    protected function setUp(): void
    {
        $this->fieldReference = new FieldReferenceResolver();

        $this->content = $this->createMock(Content::class);

        $field1 = new Field([
            'value' => new FieldType\TextLine\Value('field 1 value'),
        ]);

        $field2 = new Field([
            'value' => new FieldType\TextLine\Value('field 2 value'),
        ]);

        $this->content->method('getField')
            ->willReturnMap([
                ['field_1', null, $field1],
                ['field_2', null, $field2],
                ['non_existent_field', null, null],
            ]);
    }

    /**
     * @dataProvider getDataForTestResolveFieldValue
     */
    public function testResolveFieldValue(string $fieldInputValue, string $fieldExpectedValue): void
    {
        $result = $this->fieldReference->resolve($this->content, $fieldInputValue);

        self::assertEquals($fieldExpectedValue, $result);
    }

    /**
     * @return array<int, array<int, string>>
     */
    public function getDataForTestResolveFieldValue(): iterable
    {
        yield from [
            ['', ''],
            ['test value', 'test value'],
            ['<field_1>', 'field 1 value'],
            ['<field_X>', ''],
            ['<non_existent_field>', ''],
            ['<field_1><field_X>', 'field 1 value'],
            ['test <field_1>', 'test field 1 value'],
            ['test <field_1|field_2>', 'test field 1 value'],
            ['test <field_2|field_1>', 'test field 2 value'],
            ['test <field_1> test2 <test1>', 'test field 1 value test2'],
            ['test <wrong_field|field_1> test2 <wrong_field|test1>', 'test field 1 value test2'],
            ['test <wrong_field|field_1> test2 <field_1|wrong_field|test1>', 'test field 1 value test2 field 1 value'],
            ['test <wrong_field|field_1> test2 <wrong_field|test1|field_2>', 'test field 1 value test2 field 2 value'],
        ];
    }
}
