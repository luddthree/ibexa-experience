<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Repository\Attribute;

use Ibexa\Contracts\ProductCatalog\Local\Attribute\OptionsValidatorError;
use Ibexa\ProductCatalog\Local\Repository\Attribute\NumericOptionsValidator;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinitionOptions;
use PHPUnit\Framework\TestCase;

final class NumericOptionsValidatorTest extends TestCase
{
    /**
     * @param array<string,mixed> $options
     * @param \Ibexa\Contracts\ProductCatalog\Local\Attribute\OptionsValidatorError[] $expectedErrors
     *
     * @dataProvider dataProviderForValidate
     */
    public function testValidate(array $options, array $expectedErrors): void
    {
        $validator = new NumericOptionsValidator();

        $actualErrors = $validator->validateOptions(new AttributeDefinitionOptions($options));

        self::assertEquals($expectedErrors, $actualErrors);
    }

    /**
     * @return iterable<string,array{array<string,mixed>, OptionsValidatorError[]}>
     */
    public function dataProviderForValidate(): iterable
    {
        yield 'max < min' => [
            [
                'min' => 100,
                'max' => 1,
            ],
            [
                new OptionsValidatorError('[max]', 'Maximum value should be greater than minimum value'),
            ],
        ];

        yield 'max == min' => [
            [
                'min' => 100,
                'max' => 100,
            ],
            [
                /* Correct */
            ],
        ];
    }
}
