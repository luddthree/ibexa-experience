<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\FormBuilder\Definition;

use Ibexa\FormBuilder\Definition\FieldAttributeDefinition;
use Ibexa\FormBuilder\Definition\FieldAttributeDefinitionBuilder;
use PHPUnit\Framework\TestCase;

class FieldAttributeDefinitionBuilderTest extends TestCase
{
    /**
     * @dataProvider dataProviderForBuildDefinition
     *
     * @param string $identifier
     * @param string $name
     * @param string $type
     * @param string $category
     * @param array $constraints
     * @param array $options
     * @param $defaultValue
     */
    public function testBuildDefinition(
        string $identifier,
        string $name,
        string $type,
        string $category,
        array $constraints,
        array $options,
        $defaultValue
    ): void {
        $builder = new FieldAttributeDefinitionBuilder();
        $builder->setIdentifier($identifier);
        $builder->setName($name);
        $builder->setType($type);
        $builder->setCategory($category);
        $builder->setOptions($options);
        $builder->setConstraints($constraints);
        $builder->setDefaultValue($defaultValue);

        $this->assertEquals(
            new FieldAttributeDefinition(...\func_get_args()),
            $builder->buildDefinition()
        );
    }

    public function dataProviderForBuildDefinition(): array
    {
        return [
            [
                'identifier',
                'Name',
                'type',
                'category',
                [
                    'not_blank' => [
                        'message' => 'Name attribute value is required',
                    ],
                ],
                [
                    'a' => 'A',
                    'b' => 'B',
                    'c' => 'C',
                ],
                'value',
            ],
        ];
    }
}

class_alias(FieldAttributeDefinitionBuilderTest::class, 'EzSystems\EzPlatformFormBuilder\Tests\Definition\FieldAttributeDefinitionBuilderTest');
