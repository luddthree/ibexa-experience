<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Repository\Attribute;

use Ibexa\Contracts\ProductCatalog\Local\Attribute\OptionsValidatorError;
use Ibexa\ProductCatalog\Local\Repository\Attribute\SelectionOptionsValidator;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinitionOptions;
use PHPUnit\Framework\TestCase;

final class SelectionOptionsValidatorTest extends TestCase
{
    /**
     * @param array<string,mixed> $options
     * @param \Ibexa\Contracts\ProductCatalog\Local\Attribute\OptionsValidatorError[] $expectedErrors
     *
     * @dataProvider dataProviderForValidate
     */
    public function testValidate(array $options, array $expectedErrors): void
    {
        $validator = new SelectionOptionsValidator();

        $actualErrors = $validator->validateOptions(new AttributeDefinitionOptions($options));

        self::assertEquals($expectedErrors, $actualErrors);
    }

    /**
     * @return iterable<string,array{array<string,mixed>, OptionsValidatorError[]}>
     */
    public function dataProviderForValidate(): iterable
    {
        yield 'missing choices' => [
            [
                'choices' => [],
            ],
            [
                new OptionsValidatorError('[choices]', 'At least one option is required'),
            ],
        ];

        yield 'missing value' => [
            [
                'choices' => [
                    [
                        'label' => [
                            'eng-GB' => 'foo',
                        ],
                        'value' => null,
                    ],
                ],
            ],
            [
                new OptionsValidatorError('[choices][0][value]', 'Value should not be blank'),
            ],
        ];

        yield 'missing label' => [
            [
                'choices' => [
                    [
                        'label' => null,
                        'value' => 'foo',
                    ],
                ],
            ],
            [
                new OptionsValidatorError('[choices][0][label]', 'Label should not be blank'),
            ],
        ];

        yield 'non-unique value' => [
            [
                'choices' => [
                    [
                        'label' => [
                            'eng-GB' => 'foo',
                        ],
                        'value' => 'foo',
                    ],
                    [
                        'label' => [
                            'eng-GB' => 'foo (second time)',
                        ],
                        'value' => 'foo',
                    ],
                ],
            ],
            [
                new OptionsValidatorError('[choices][1][value]', 'Duplicated value'),
            ],
        ];

        yield 'strict comparison' => [
            [
                'choices' => [
                    [
                        'label' => [
                            'eng-GB' => 'foo',
                        ],
                        'value' => '0',
                    ],
                    [
                        'label' => [
                            'eng-GB' => 'bar',
                        ],
                        'value' => 0,
                    ],
                ],
            ],
            [
                new OptionsValidatorError('[choices][1][value]', 'Duplicated value'),
            ],
        ];

        yield 'missing label translation' => [
            [
                'choices' => [
                    [
                        'label' => [
                            'eng-GB' => 'Foo',
                            'pol-PL' => null,
                        ],
                        'value' => 'foo',
                    ],
                ],
            ],
            [
                new OptionsValidatorError('[choices][0][label][pol-PL]', 'Label should not be blank'),
            ],
        ];
    }
}
