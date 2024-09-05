<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\FormBuilder\FieldType\Field;

use Ibexa\Contracts\FormBuilder\FieldType\Field\FieldMapperInterface;
use Ibexa\FormBuilder\FieldType\Field\FieldMapperRegistry;
use PHPUnit\Framework\TestCase;

class FieldMapperRegistryTest extends TestCase
{
    public function testHasMapper(): void
    {
        $registry = $this->createRegistry([
            $this->createFieldMapperMock('existing'),
        ]);

        $this->assertTrue($registry->hasMapper('existing'));
        $this->assertFalse($registry->hasMapper('non-existing'));
    }

    public function testGetMapper(): void
    {
        $mappers = [
            $this->createFieldMapperMock('foo'),
            $this->createFieldMapperMock('bar'),
            $this->createFieldMapperMock('baz'),
        ];

        $this->assertEquals($mappers[0], $this->createRegistry($mappers)->getMapper('foo'));
    }

    public function testGetMappers(): void
    {
        $mappers = [
            $this->createFieldMapperMock('foo'),
            $this->createFieldMapperMock('bar'),
            $this->createFieldMapperMock('baz'),
        ];

        $this->assertEquals($mappers, $this->createRegistry($mappers)->getMappers());
    }

    private function createRegistry(array $mappers): FieldMapperRegistry
    {
        return new FieldMapperRegistry($mappers);
    }

    private function createFieldMapperMock(string $identifier): FieldMapperInterface
    {
        $mapper = $this->createMock(FieldMapperInterface::class);
        $mapper
            ->expects($this->once())
            ->method('getSupportedField')
            ->willReturn($identifier);

        return $mapper;
    }

    /**
     * @depends testGetMappers
     */
    public function testAddMapper(): void
    {
        $fooMapper = $this->createFieldMapperMock('foo');
        $barMapper = $this->createFieldMapperMock('bar');

        $registry = $this->createRegistry([
            $fooMapper,
        ]);

        $registry->addMapper($barMapper);

        $this->assertEquals([$fooMapper, $barMapper], $registry->getMappers());
    }
}

class_alias(FieldMapperRegistryTest::class, 'EzSystems\EzPlatformFormBuilder\Tests\FieldType\Field\FieldMapperRegistryTest');
