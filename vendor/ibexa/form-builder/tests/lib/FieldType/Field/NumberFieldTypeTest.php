<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\FormBuilder\FieldType\Field;

use Ibexa\Contracts\FormBuilder\FieldType\Model\Field;
use Ibexa\FormBuilder\Form\Type\Field\NumberFieldType;
use Symfony\Component\Form\Test\TypeTestCase;

final class NumberFieldTypeTest extends TypeTestCase
{
    /**
     * @phpstan-return iterable<array{
     *     float|null,
     *     float|null,
     *     float|string,
     *     float|string,
     *     4?: bool,
     * }>
     */
    public function modelTransformerProvider(): iterable
    {
        yield [
            2.0, 1.0, 2.0, 1.0,
        ];
        yield [
            null, 1.0, '', 1.0,
        ];
        yield [
            2.0, null, 2.0, '',
        ];
        yield [
            0, 1.0, 0, 1.0,
        ];
        yield [
            2.0, 0, 2.0, 0,
        ];
        yield [
            0.0, 1.0, 0.0, 1.0,
        ];
        yield [
            2.0, 0.0, 2.0, 0.0,
        ];
        yield [
            0, 1.0, '0', 1.0,
        ];
        yield [
            2.0, 0, 2.0, '0',
        ];
        yield [
            null, 1.0, 'foobar', 1.0,
        ];
        yield [
            2.0, null, 2.0, 'foobar', true, //causes exception in Symfony\Component\Form\Extension\Core\DataTransformer\NumberToLocalizedStringTransformer
        ];
    }

    /**
     * @dataProvider modelTransformerProvider
     *
     * @phpstan-param float|string $defaultValue
     * @phpstan-param float|string $submittedValue
     */
    public function testModelTransformer(
        ?float $expectedTransformation,
        ?float $expectedReverseTransformation,
        $defaultValue,
        $submittedValue,
        bool $expectTransformationException = false
    ): void {
        $form = $this->factory->create(NumberFieldType::class, $defaultValue, ['field' => new Field()]);

        self::assertEquals($expectedTransformation, $form->getViewData(), 'Transformation failed');

        $form->submit($submittedValue);

        self::assertEquals($expectTransformationException, !$form->isSynchronized(), 'Transformation exception test failed');
        self::assertEquals($expectedReverseTransformation, $form->getData(), 'Reverse transformation failed');
    }
}
