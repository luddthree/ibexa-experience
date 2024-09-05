<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ActivityLog;

use Ibexa\ActivityLog\ObjectClassToShortNameMapper;
use Ibexa\Contracts\ActivityLog\ClassNameMapperInterface;
use PHPUnit\Framework\TestCase;

final class ObjectClassToShortNameMapperTest extends TestCase
{
    private ObjectClassToShortNameMapper $mapper;

    protected function setUp(): void
    {
        /** @var class-string $fooClass */
        $fooClass = 'foo_class';
        $mock1 = $this->createMock(ClassNameMapperInterface::class);
        $mock1
            ->expects(self::once())
            ->method('getClassNameToShortNameMap')
            ->willReturn([$fooClass => 'foo']);

        /** @var class-string $barClass */
        $barClass = 'bar_class';
        $mock2 = $this->createMock(ClassNameMapperInterface::class);
        $mock2
            ->expects(self::once())
            ->method('getClassNameToShortNameMap')
            ->willReturn([$barClass => 'bar']);

        $this->mapper = new ObjectClassToShortNameMapper([$mock1, $mock2]);
    }

    public function testGetShortNameForObjectClass(): void
    {
        /** @var class-string $class */
        $class = 'foo_class';
        $shortName = $this->mapper->getShortNameForObjectClass($class);
        self::assertSame('foo', $shortName);
    }

    public function testGetNonExistentObjectClass(): void
    {
        /** @var class-string $class */
        $class = 'non-existent';
        $shortName = $this->mapper->getShortNameForObjectClass($class);
        self::assertNull($shortName);
    }
}
