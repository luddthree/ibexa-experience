<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\FormBuilder\FieldType\Field\Mapper;

use Ibexa\Contracts\FormBuilder\FieldType\Field\FieldMapperInterface;
use Ibexa\Contracts\FormBuilder\FieldType\Model\Attribute;
use Ibexa\Contracts\FormBuilder\FieldType\Model\Field;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;

abstract class AbstractFieldMapperTest extends TestCase
{
    /** @var \Ibexa\Contracts\FormBuilder\FieldType\Field\FieldMapperInterface */
    protected $mapper;

    protected function setUp(): void
    {
        $this->mapper = $this->createMapper();
    }

    /**
     * @dataProvider dataProviderForMapForm
     *
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\Field $field
     * @param array $constraints
     * @param string $expectedType
     * @param array $expectedOptions
     */
    public function testMapForm(Field $field, array $constraints, string $expectedType, array $expectedOptions): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects($this->once())
            ->method('add')
            ->with($field->getId(), $expectedType, $expectedOptions);

        $this->mapper->mapField($builder, $field, $constraints);
    }

    public function testGetSupportedField(): void
    {
        $this->assertEquals($this->getExpectedSupportedField(), $this->mapper->getSupportedField());
    }

    /**
     * Created field with specified attributes values.
     *
     * @param string $name
     * @param array $values
     *
     * @return \Ibexa\Contracts\FormBuilder\FieldType\Model\Field
     */
    protected function createField(string $name, array $values): Field
    {
        $attributes = [];
        foreach ($values as $identifier => $value) {
            if (\is_array($value)) {
                $value = json_encode($value);
            }
            if (\is_bool($value)) {
                $value = (string) $value;
            }
            $attributes[] = new Attribute($identifier, $value);
        }

        return new Field('1', $this->getExpectedSupportedField(), $name, $attributes);
    }

    /**
     * Data provider for FieldMapperInterface::mapForms tests.
     *
     * Returns list of the following tuples:
     *
     *  * Mapped field (\Ibexa\Contracts\FormBuilder\FieldType\Model\Field)
     *  * Mapped list of constraints (array)
     *  * Expected form type (string)
     *  * Expected options list (array)
     *
     * @return array
     */
    abstract public function dataProviderForMapForm(): array;

    /**
     * Creates instance of the tested Field Mapper.
     *
     * @return \Ibexa\Contracts\FormBuilder\FieldType\Field\FieldMapperInterface
     */
    abstract protected function createMapper(): FieldMapperInterface;

    /**
     * Returns expected value returned by getSupportedField method.
     *
     * @see \Ibexa\Contracts\FormBuilder\FieldType\Field\FieldMapperInterface::getSupportedField
     *
     * @return string
     */
    abstract protected function getExpectedSupportedField(): string;
}

class_alias(AbstractFieldMapperTest::class, 'EzSystems\EzPlatformFormBuilder\Tests\FieldType\Field\Mapper\AbstractFieldMapperTest');
