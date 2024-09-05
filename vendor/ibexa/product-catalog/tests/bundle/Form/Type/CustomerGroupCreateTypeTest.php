<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\Type;

use Ibexa\Bundle\ProductCatalog\Form\Data\CustomerGroupCreateData;
use Ibexa\Bundle\ProductCatalog\Form\Type\CustomerGroupCreateType;
use Ibexa\Bundle\ProductCatalog\Validator\Constraints\UniqueCustomerGroupIdentifierValidator;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\ConstraintValidatorInterface;

/**
 * @extends \Ibexa\Tests\Bundle\ProductCatalog\Form\Type\AbstractCustomerGroupTypeTest<
 *     \Ibexa\Bundle\ProductCatalog\Form\Data\CustomerGroupCreateData
 * >
 */
final class CustomerGroupCreateTypeTest extends AbstractCustomerGroupTypeTest
{
    protected function getData(): object
    {
        return new CustomerGroupCreateData();
    }

    protected function getForm(object $model): FormInterface
    {
        return $this->factory->create(CustomerGroupCreateType::class, $model);
    }

    public function provideValidData(): iterable
    {
        $correctData = [
            'language' => 'eng-GB',
            'name' => 'foo_name',
            'identifier' => 'foo_identifier',
            'description' => 'foo_description',
            'global_price_rate' => 10.555,
        ];

        yield [
            $correctData,
            static function (CustomerGroupCreateData $model): void {
                self::assertSame($model->getName(), 'foo_name');
                self::assertSame($model->getIdentifier(), 'foo_identifier');
                self::assertSame($model->getDescription(), 'foo_description');
                self::assertSame($model->getGlobalPriceRate(), '10.56');
            },
        ];

        yield [
            array_merge($correctData, [
                'name' => str_repeat('a', 150),
                'identifier' => str_repeat('a', 64),
                'description' => str_repeat('a', 10000),
            ]),
            static function (CustomerGroupCreateData $model): void {
                self::assertSame($model->getName(), str_repeat('a', 150));
                self::assertSame($model->getIdentifier(), str_repeat('a', 64));
                self::assertSame($model->getDescription(), str_repeat('a', 10000));
            },
        ];

        yield [
            array_merge($correctData, [
                'global_price_rate' => 0,
            ]),
            static function (CustomerGroupCreateData $model): void {
                self::assertSame($model->getGlobalPriceRate(), '0');
            },
        ];

        yield [
            array_merge($correctData, [
                'global_price_rate' => 100,
            ]),
            static function (CustomerGroupCreateData $model): void {
                self::assertSame($model->getGlobalPriceRate(), '100');
            },
        ];
    }

    public function provideInvalidData(): iterable
    {
        yield 'No data' => [
            [],
        ];

        $correctData = [
            'name' => 'foo_name',
            'identifier' => 'foo_identifier',
            'description' => 'foo_description',
            'global_price_rate' => 10,
        ];

        yield 'Empty "name"' => [
            array_merge($correctData, [
                'name' => '',
            ]),
        ];

        yield 'Too long "name" (over 150)' => [
            array_merge($correctData, [
                'name' => str_repeat('a', 151),
            ]),
        ];

        yield 'Empty "identifier"' => [
            array_merge($correctData, [
                'identifier' => '',
            ]),
        ];

        yield '"identifier" containing spaces (failing regex)' => [
            array_merge($correctData, [
                'identifier' => 'foo bar',
            ]),
        ];

        yield 'Too long "identifier" (over 64)' => [
            array_merge($correctData, [
                'identifier' => str_repeat('a', 65),
            ]),
        ];

        yield 'Too long "description" (over 10000)' => [
            array_merge($correctData, [
                'identifier' => str_repeat('a', 10001),
            ]),
        ];

        yield 'Too low "global_price_rate" (below -100%)' => [
            array_merge($correctData, [
                'global_price_rate' => -101,
            ]),
        ];
    }

    protected function getConstraintValidatorMocks(): iterable
    {
        yield UniqueCustomerGroupIdentifierValidator::class => $this->createMock(ConstraintValidatorInterface::class);
    }
}
