<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\Type;

use Ibexa\Bundle\ProductCatalog\Form\Data\Price\Create\ProductPriceCreateData;
use Ibexa\Bundle\ProductCatalog\Form\Type\ProductPriceType;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @covers \Ibexa\Bundle\ProductCatalog\Form\Type\ProductPriceType
 */
final class ProductPriceTypeTest extends AbstractTypeTestCase
{
    /**
     * @dataProvider provideValidData
     *
     * @param array<string, mixed> $formData
     * @param callable(\Ibexa\Bundle\ProductCatalog\Form\Data\Price\Create\ProductPriceCreateData): void $expectation
     */
    public function testSubmitValidData(array $formData, callable $expectation): void
    {
        $model = $this->getInitialFormData();
        $form = $this->createForm($model);
        $form->submit($formData);

        self::assertTrue($form->isSynchronized());
        self::assertTrue($form->isValid(), (string) $form->getErrors(true, true));

        $expectation($model);
    }

    /**
     * @return iterable<array{array<string, mixed>, callable(\Ibexa\Bundle\ProductCatalog\Form\Data\Price\Create\ProductPriceCreateData): void}>
     */
    public function provideValidData(): iterable
    {
        yield [
            [
                'base_price' => '1000',
            ],
            static function (ProductPriceCreateData $model): void {
                self::assertSame($model->getBasePrice(), '1000');
            },
        ];
    }

    /**
     * @dataProvider provideInvalidData
     *
     * @param array<string, mixed> $formData
     */
    public function testSubmitInvalidData(array $formData): void
    {
        $model = $this->getInitialFormData();
        $form = $this->createForm($model);
        $form->submit($formData);

        self::assertTrue($form->isSynchronized());
        self::assertFalse($form->isValid(), (string) $form->getErrors(true, true));
    }

    /**
     * @return iterable<string, array{array<string, mixed>}>
     */
    public function provideInvalidData(): iterable
    {
        yield 'Empty form' => [
            [],
        ];

        yield 'Base price is negative (-1000)' => [
            [
                'base_price' => '-1000',
            ],
        ];
    }

    private function getInitialFormData(): ProductPriceCreateData
    {
        $product = $this->createMock(ProductInterface::class);
        $currency = $this->createMock(CurrencyInterface::class);

        return new ProductPriceCreateData($product, $currency);
    }

    private function createForm(ProductPriceCreateData $model): FormInterface
    {
        return $this->factory->create(ProductPriceType::class, $model, [
            'currency' => $this->createMock(CurrencyInterface::class),
        ]);
    }
}
