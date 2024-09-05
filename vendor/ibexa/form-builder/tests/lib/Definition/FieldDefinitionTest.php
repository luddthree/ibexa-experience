<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\FormBuilder\Definition;

use Ibexa\FormBuilder\Definition\FieldAttributeDefinition;
use Ibexa\FormBuilder\Definition\FieldDefinition;
use Ibexa\FormBuilder\Definition\FieldValidatorDefinition;
use Ibexa\FormBuilder\Exception\FieldAttributeDefinitionNotFoundException;
use Ibexa\FormBuilder\Exception\FieldValidatorDefinitionNotFoundException;
use PHPUnit\Framework\TestCase;

class FieldDefinitionTest extends TestCase
{
    private const FIELD_ATTRIBUTE = 'foo';
    private const FIELD_VALIDATOR = 'bar';
    private const NO_EXISTING_IDENTIFIER = 'baz';

    /** @var \Ibexa\FormBuilder\Definition\FieldDefinition */
    private $fieldDefinition;

    /** @var \Ibexa\FormBuilder\Definition\FieldAttributeDefinition */
    private $fieldAttributeDefinition;

    /** @var \Ibexa\FormBuilder\Definition\FieldValidatorDefinition */
    private $fieldValidatorDefinition;

    protected function setUp(): void
    {
        $this->fieldAttributeDefinition = $this->createFieldAttributeDefinition(self::FIELD_ATTRIBUTE);
        $this->fieldValidatorDefinition = $this->createFieldValidatorDefinition(self::FIELD_VALIDATOR);
        $this->fieldDefinition = $this->createFieldDefinition(
            'foo',
            [
                $this->fieldAttributeDefinition,
            ],
            [
                $this->fieldValidatorDefinition,
            ]
        );
    }

    public function testHasAttribute(): void
    {
        $this->assertTrue($this->fieldDefinition->hasAttribute(self::FIELD_ATTRIBUTE));
        $this->assertFalse($this->fieldDefinition->hasAttribute(self::FIELD_VALIDATOR));
    }

    public function testGetAttribute(): void
    {
        $this->assertSame($this->fieldAttributeDefinition, $this->fieldDefinition->getAttribute(self::FIELD_ATTRIBUTE));
    }

    public function testGetAttributeThrowException(): void
    {
        $this->expectException(FieldAttributeDefinitionNotFoundException::class);

        $this->fieldDefinition->getAttribute(self::NO_EXISTING_IDENTIFIER);
    }

    public function testHasValidator(): void
    {
        $this->assertTrue($this->fieldDefinition->hasValidator(self::FIELD_VALIDATOR));
        $this->assertFalse($this->fieldDefinition->hasValidator(self::NO_EXISTING_IDENTIFIER));
    }

    public function testGetValidator(): void
    {
        $this->assertSame($this->fieldValidatorDefinition, $this->fieldDefinition->getValidator(self::FIELD_VALIDATOR));
    }

    public function testGetValidatorThrowException(): void
    {
        $this->expectException(FieldValidatorDefinitionNotFoundException::class);

        $this->fieldDefinition->getValidator(self::NO_EXISTING_IDENTIFIER);
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
}

class_alias(FieldDefinitionTest::class, 'EzSystems\EzPlatformFormBuilder\Tests\Definition\FieldDefinitionTest');
