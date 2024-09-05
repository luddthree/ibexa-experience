<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\FormBuilder\ApplicationConfig;

use Ibexa\Contracts\FormBuilder\FieldType\Model\Validator;
use Ibexa\FormBuilder\ApplicationConfig\FormBuilderConfigProvider;
use Ibexa\FormBuilder\Definition\FieldAttributeDefinition;
use Ibexa\FormBuilder\Definition\FieldDefinition;
use Ibexa\FormBuilder\Definition\FieldDefinitionFactory;
use Ibexa\FormBuilder\Definition\FieldValidatorDefinition;
use PHPUnit\Framework\TestCase;

class FormBuilderConfigProviderTest extends TestCase
{
    /**
     * @dataProvider dataProviderForGetConfig
     *
     * @param array $definitions
     * @param array $expected
     */
    public function testGetConfig(array $definitions, array $expected): void
    {
        $fieldDefinitionFactory = $this->createMock(FieldDefinitionFactory::class);
        $fieldDefinitionFactory
            ->expects($this->once())
            ->method('getFieldDefinitions')
            ->willReturn($definitions);

        $this->assertEquals($expected, (new FormBuilderConfigProvider($fieldDefinitionFactory))->getConfig());
    }

    public function dataProviderForGetConfig(): array
    {
        return [
            [
                [
                    new FieldDefinition('foo', 'Foo'),
                    new FieldDefinition(
                        'baz',
                        'Baz',
                        'custom',
                        [
                            new FieldAttributeDefinition('a', 'Attribute with default values', 'string'),
                            new FieldAttributeDefinition('b', 'Attribute with options', 'choice', 'default', [], [
                                'A' => 'a',
                                'B' => 'b',
                                'C' => 'c',
                            ], 'a'),
                            new FieldAttributeDefinition('c', 'Attribute with constraints', 'integer', 'custom', [
                                'not_blank' => [],
                                'range' => [
                                    'min' => 100,
                                    'max' => 999,
                                ],
                            ]),
                        ],
                        [
                            new FieldValidatorDefinition(
                                'd',
                                'default',
                                [
                                    new Validator(
                                        'identifier',
                                        'value'
                                    ),
                                ],
                                [
                                    'A' => 'a',
                                    'B' => 'b',
                                ],
                                'defaultValue'
                            ),
                        ],
                        '/path/to/thumbnail.svg'
                    ),
                ],
                [
                    'fieldsConfig' => [
                        [
                            'identifier' => 'foo',
                            'name' => 'Foo',
                            'category' => 'default',
                            'attributes' => [],
                            'validators' => [],
                            'thumbnail' => null,
                        ],
                        [
                            'identifier' => 'baz',
                            'name' => 'Baz',
                            'category' => 'custom',
                            'attributes' => [
                                [
                                    'identifier' => 'a',
                                    'name' => 'Attribute with default values',
                                    'type' => 'string',
                                    'category' => 'default',
                                    'constraints' => [],
                                    'options' => [],
                                    'defaultValue' => null,
                                ],
                                [
                                    'identifier' => 'b',
                                    'name' => 'Attribute with options',
                                    'type' => 'choice',
                                    'category' => 'default',
                                    'constraints' => [],
                                    'options' => [
                                        'A' => 'a',
                                        'B' => 'b',
                                        'C' => 'c',
                                    ],
                                    'defaultValue' => 'a',
                                ],
                                [
                                    'identifier' => 'c',
                                    'name' => 'Attribute with constraints',
                                    'type' => 'integer',
                                    'category' => 'custom',
                                    'constraints' => [
                                        'not_blank' => [],
                                        'range' => [
                                            'min' => 100,
                                            'max' => 999,
                                        ],
                                    ],
                                    'options' => [],
                                    'defaultValue' => null,
                                ],
                            ],
                            'validators' => [
                                [
                                    'identifier' => 'd',
                                    'category' => 'default',
                                    'constraints' => [
                                        [
                                            'identifier' => 'identifier',
                                            'value' => 'value',
                                        ],
                                    ],
                                    'options' => [
                                        'A' => 'a',
                                        'B' => 'b',
                                    ],
                                    'defaultValue' => 'defaultValue',
                                ],
                            ],
                            'thumbnail' => '/path/to/thumbnail.svg',
                        ],
                    ],
                ],
            ],
        ];
    }
}

class_alias(FormBuilderConfigProviderTest::class, 'EzSystems\EzPlatformFormBuilder\Tests\ApplicationConfig\FormBuilderConfigProviderTest');
