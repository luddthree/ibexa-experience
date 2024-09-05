<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy\Form\DataTransformer;

use Ibexa\Taxonomy\FieldType\TaxonomyEntry\Value;
use Ibexa\Taxonomy\Form\DataTransformer\TaxonomyEntryFieldTypeValueDataTransformer;
use stdClass;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class TaxonomyEntryFieldTypeValueDataTransformerTest extends AbstractTaxonomyEntryTransformerTest
{
    private TaxonomyEntryFieldTypeValueDataTransformer $transformer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->transformer = new TaxonomyEntryFieldTypeValueDataTransformer($this->taxonomyService);
    }

    /**
     * @dataProvider dataProviderForTestTransformDataProvider
     */
    public function testTransform(?Value $value, ?int $expected): void
    {
        self::assertEquals($expected, $this->transformer->transform($value));
    }

    /**
     * @return iterable<string, mixed>
     */
    public function dataProviderForTestTransformDataProvider(): iterable
    {
        yield 'null' => [null, null];
        yield 'empty value' => [new Value(), null];
        yield 'value with entry' => [new Value($this->createEntry(1)), 1];
    }

    public function testTransformWithInvalidInput(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage(sprintf(
            'Value of type %s expected',
            Value::class
        ));

        /** @phpstan-ignore-next-line */
        $this->transformer->transform(new stdClass());
    }

    /**
     * @dataProvider dataProviderForTestReverseTransformDataProvider
     *
     * @param int|null $value
     */
    public function testReverseTransform($value, Value $expected): void
    {
        self::assertEquals($expected, $this->transformer->reverseTransform($value));
    }

    /**
     * @return iterable<string, mixed>
     */
    public function dataProviderForTestReverseTransformDataProvider(): iterable
    {
        yield 'null' => [null, new Value()];
        yield 'int' => [1, new Value($this->createEntry(1))];
        yield 'string' => ['1', new Value($this->createEntry(1))];
    }
}
