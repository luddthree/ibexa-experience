<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\FormBuilder\Definition;

use Ibexa\FormBuilder\Definition\FieldAttributeDefinition;
use Ibexa\FormBuilder\Definition\FieldDefinition;
use Ibexa\FormBuilder\Definition\FieldDefinitionBuilder;
use Ibexa\FormBuilder\Definition\FieldValidatorDefinition;
use PHPUnit\Framework\TestCase;

class FieldDefinitionBuilderTest extends TestCase
{
    /** @var \Ibexa\FormBuilder\Definition\FieldDefinitionBuilder */
    private $builder;

    protected function setUp(): void
    {
        $builder = new FieldDefinitionBuilder();
        $builder->setIdentifier('identifier');
        $builder->setCategory('category');
        $builder->setName('name');
        $builder->setThumbnail('thumbnail');
        $builder->setAttributes([
            new FieldAttributeDefinition('foo', 'Foo', 'string'),
            new FieldAttributeDefinition('bar', 'Boo', 'choice'),
            new FieldAttributeDefinition('baz', 'Boo', 'integer'),
        ]);
        $builder->setValidators([
            new FieldValidatorDefinition('foo', 'Foo'),
        ]);

        $this->builder = $builder;
    }

    public function testBuildDefinition(): void
    {
        $expected = new FieldDefinition(
            'identifier',
            'name',
            'category',
            [
                new FieldAttributeDefinition('foo', 'Foo', 'string'),
                new FieldAttributeDefinition('bar', 'Boo', 'choice'),
                new FieldAttributeDefinition('baz', 'Boo', 'integer'),
            ],
            [
                new FieldValidatorDefinition('foo', 'Foo'),
            ],
            'thumbnail'
        );

        $this->assertEquals($expected, $this->builder->buildDefinition());
    }

    public function testRemoveValidator(): void
    {
        $expected = new FieldDefinition(
            'identifier',
            'name',
            'category',
            [
                new FieldAttributeDefinition('foo', 'Foo', 'string'),
                new FieldAttributeDefinition('bar', 'Boo', 'choice'),
                new FieldAttributeDefinition('baz', 'Boo', 'integer'),
            ],
            [],
            'thumbnail'
        );

        $this->builder->removeValidator($this->builder->getValidators()[0]);

        $this->assertEquals($expected, $this->builder->buildDefinition());
    }

    public function testAddValidator(): void
    {
        $expected = new FieldDefinition(
            'identifier',
            'name',
            'category',
            [
                new FieldAttributeDefinition('foo', 'Foo', 'string'),
                new FieldAttributeDefinition('bar', 'Boo', 'choice'),
                new FieldAttributeDefinition('baz', 'Boo', 'integer'),
            ],
            [
                new FieldValidatorDefinition('foo', 'Foo'),
                new FieldValidatorDefinition('bar', 'Bar'),
            ],
            'thumbnail'
        );

        $this->builder->addValidator(new FieldValidatorDefinition('bar', 'Bar'));

        $this->assertEquals($expected, $this->builder->buildDefinition());
    }

    public function testRemoveAttribute(): void
    {
        $expected = new FieldDefinition(
            'identifier',
            'name',
            'category',
            [
                1 => new FieldAttributeDefinition('bar', 'Boo', 'choice'),
                2 => new FieldAttributeDefinition('baz', 'Boo', 'integer'),
            ],
            [
                new FieldValidatorDefinition('foo', 'Foo'),
            ],
            'thumbnail'
        );

        // Because `unset` indexes are still the same.
        $this->builder->removeAttribute($this->builder->getAttributes()[0]);

        $this->assertEquals($expected, $this->builder->buildDefinition());
    }

    public function testAddAttribute(): void
    {
        $expected = new FieldDefinition(
            'identifier',
            'name',
            'category',
            [
                new FieldAttributeDefinition('foo', 'Foo', 'string'),
                new FieldAttributeDefinition('bar', 'Boo', 'choice'),
                new FieldAttributeDefinition('baz', 'Boo', 'integer'),
                new FieldAttributeDefinition('bam', 'Bam', 'integer'),
            ],
            [
                new FieldValidatorDefinition('foo', 'Foo'),
            ],
            'thumbnail'
        );

        // Because `unset` indexes are still the same.
        $this->builder->addAttribute(new FieldAttributeDefinition('bam', 'Bam', 'integer'));

        $this->assertEquals($expected, $this->builder->buildDefinition());
    }
}

class_alias(FieldDefinitionBuilderTest::class, 'EzSystems\EzPlatformFormBuilder\Tests\Definition\FieldDefinitionBuilderTest');
