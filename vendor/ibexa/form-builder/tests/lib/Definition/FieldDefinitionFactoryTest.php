<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\FormBuilder\Definition;

use Ibexa\FormBuilder\Definition\FieldAttributeDefinition;
use Ibexa\FormBuilder\Definition\FieldDefinition;
use Ibexa\FormBuilder\Definition\FieldDefinitionFactory;
use Ibexa\FormBuilder\Definition\FieldValidatorDefinition;
use Ibexa\FormBuilder\Event\FieldAttributeDefinitionEvent;
use Ibexa\FormBuilder\Event\FieldDefinitionEvent;
use Ibexa\FormBuilder\Event\FieldValidatorDefinitionEvent;
use Ibexa\FormBuilder\Exception\FieldDefinitionNotFoundException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class FieldDefinitionFactoryTest extends TestCase
{
    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $eventDispatcher;

    protected function setUp(): void
    {
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
    }

    public function testHasFieldDefinition(): void
    {
        $factory = new FieldDefinitionFactory($this->eventDispatcher, [
            'existing' => $this->createFieldConfiguration('existing'),
        ]);

        $this->assertTrue($factory->hasFieldDefinition('existing'));
        $this->assertFalse($factory->hasFieldDefinition('non-existing'));
    }

    public function testGetFieldDefinitions(): void
    {
        $factory = new FieldDefinitionFactory($this->eventDispatcher, [
            'foo' => $this->createFieldConfiguration('foo'),
            'bar' => $this->createFieldConfiguration('bar'),
            'baz' => $this->createFieldConfiguration('baz'),
        ]);

        $this->assertEquals([
            $this->createFieldDefinition('foo'),
            $this->createFieldDefinition('bar'),
            $this->createFieldDefinition('baz'),
        ], $factory->getFieldDefinitions());
    }

    public function testGetNonExistingFieldDefinition(): void
    {
        $this->expectException(FieldDefinitionNotFoundException::class);

        $factory = new FieldDefinitionFactory($this->eventDispatcher, [
            'existing' => $this->createFieldConfiguration('existing'),
        ]);

        $factory->getFieldDefinition('non-existing');
    }

    /**
     * @dataProvider dataProviderForGetFieldDefinition
     *
     * @param string $fieldIdentifier
     * @param array $configuration
     * @param array $expectedEvents
     * @param \Ibexa\FormBuilder\Definition\FieldDefinition $expectedFieldDefinition
     */
    public function testGetFieldDefinition(
        string $fieldIdentifier,
        array $configuration,
        array $expectedEvents,
        FieldDefinition $expectedFieldDefinition
    ): void {
        $factory = new FieldDefinitionFactory($this->eventDispatcher, $configuration);

        $this->eventDispatcher
            ->expects($this->exactly(\count($expectedEvents)))
            ->method('dispatch')
            ->withConsecutive(...array_map(function (array $event) {
                return [
                    $this->isInstanceOf($event['class']),
                    $this->equalTo($event['name']),
                ];
            }, $expectedEvents));

        $this->assertEquals($expectedFieldDefinition, $factory->getFieldDefinition($fieldIdentifier));
    }

    public function dataProviderForGetFieldDefinition(): array
    {
        return [
            [
                'foobar',
                [
                    'foobar' => $this->createFieldConfiguration('foobar', [
                        'foo' => $this->createFieldAttributeConfiguration('foo'),
                        'bar' => $this->createFieldAttributeConfiguration('bar'),
                    ], [
                        'foo' => $this->createFieldValidatorConfiguration('foo'),
                        'bar' => $this->createFieldValidatorConfiguration('bar'),
                    ]),
                ],
                [
                    [
                        'name' => 'ezplatform.form_builder.field.attribute',
                        'class' => FieldAttributeDefinitionEvent::class,
                    ],
                    [
                        'name' => 'ezplatform.form_builder.field.foobar.attribute.foo',
                        'class' => FieldAttributeDefinitionEvent::class,
                    ],
                    [
                        'name' => 'ezplatform.form_builder.field.attribute',
                        'class' => FieldAttributeDefinitionEvent::class,
                    ],
                    [
                        'name' => 'ezplatform.form_builder.field.foobar.attribute.bar',
                        'class' => FieldAttributeDefinitionEvent::class,
                    ],
                    [
                        'name' => 'ezplatform.form_builder.field.attribute',
                        'class' => FieldValidatorDefinitionEvent::class,
                    ],
                    [
                        'name' => 'ezplatform.form_builder.field.foobar.validator.foo',
                        'class' => FieldValidatorDefinitionEvent::class,
                    ],
                    [
                        'name' => 'ezplatform.form_builder.field.attribute',
                        'class' => FieldValidatorDefinitionEvent::class,
                    ],
                    [
                        'name' => 'ezplatform.form_builder.field.foobar.validator.bar',
                        'class' => FieldValidatorDefinitionEvent::class,
                    ],
                    [
                        'name' => 'ezplatform.form_builder.field',
                        'class' => FieldDefinitionEvent::class,
                    ],
                    [
                        'name' => 'ezplatform.form_builder.field.foobar',
                        'class' => FieldDefinitionEvent::class,
                    ],
                ],
                $this->createFieldDefinition(
                    'foobar',
                    [
                        $this->createFieldAttributeDefinition('foo'),
                        $this->createFieldAttributeDefinition('bar'),
                    ],
                    [
                        $this->createFieldValidatorDefinition('foo'),
                        $this->createFieldValidatorDefinition('bar'),
                    ]
                ),
            ],
        ];
    }

    private function createFieldAttributeConfiguration(string $identifier): array
    {
        return [
            'name' => ucfirst($identifier),
            'category' => 'category',
            'type' => 'type',
            'options' => [
                'A' => 'a',
                'B' => 'b',
                'C' => 'c',
            ],
            'default_value' => 'default',
            'validators' => [
                'not_blank' => [
                    'message' => 'This attribute is required.',
                ],
            ],
        ];
    }

    private function createFieldAttributeDefinition(string $identifier): FieldAttributeDefinition
    {
        return new FieldAttributeDefinition(
            $identifier,
            ucfirst($identifier),
            'type',
            'category',
            [
                'not_blank' => [
                    'message' => 'This attribute is required.',
                ],
            ],
            [
                'A' => 'a',
                'B' => 'b',
                'C' => 'c',
            ],
            'default'
        );
    }

    private function createFieldValidatorConfiguration(string $identifier)
    {
        return [
            'name' => ucfirst($identifier),
            'category' => 'default',
            'options' => [],
            'default_value' => 'default',
            'validators' => [],
        ];
    }

    private function createFieldValidatorDefinition(string $identifier): FieldValidatorDefinition
    {
        return new FieldValidatorDefinition(
            $identifier,
            FieldValidatorDefinition::DEFAULT_CATEGORY,
            [],
            [],
            'default'
        );
    }

    private function createFieldConfiguration(string $identifier, array $attributes = [], array $validators = []): array
    {
        return [
            'name' => ucfirst($identifier),
            'category' => 'custom',
            'attributes' => $attributes,
            'validators' => $validators,
            'thumbnail' => '/path/to/' . $identifier . '.svg',
        ];
    }

    private function createFieldDefinition(
        string $identifier,
        array $attributes = [],
        array $validators = []
    ): FieldDefinition {
        return new FieldDefinition(
            $identifier,
            ucfirst($identifier),
            'custom',
            $attributes,
            $validators,
            '/path/to/' . $identifier . '.svg'
        );
    }
}

class_alias(FieldDefinitionFactoryTest::class, 'EzSystems\EzPlatformFormBuilder\Tests\Definition\FieldDefinitionFactoryTest');
