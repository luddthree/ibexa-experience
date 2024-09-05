<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration\Generator\CriterionGenerator;

use Ibexa\Migration\Generator\CriterionGenerator\GeneratorInterface;
use Ibexa\Migration\Generator\CriterionGenerator\GeneratorRegistry;
use Ibexa\Migration\Generator\Exception\UnknownMatchPropertyException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Migration\Generator\CriterionGenerator\GeneratorRegistry
 */
final class GeneratorRegistryTest extends TestCase
{
    public function testExceptionIsThrownWhenBuilderIsNotFoundInEmptyRegistry(): void
    {
        $registry = new GeneratorRegistry([]);

        $this->expectException(UnknownMatchPropertyException::class);
        $this->expectExceptionMessage('Unknown matchProperty value: foo. Supported: []');

        $registry->find('foo');
    }

    public function testExceptionIsThrownWhenBuilderIsNotFound(): void
    {
        $registry = new GeneratorRegistry([
            'bar' => $this->createMock(GeneratorInterface::class),
        ]);

        $this->expectException(UnknownMatchPropertyException::class);
        $this->expectExceptionMessage('Unknown matchProperty value: foo. Supported: [bar]');

        $registry->find('foo');
    }

    public function testFindsBuilderByCriterionIdentifier(): void
    {
        $builder1 = $this->createMock(GeneratorInterface::class);
        $builder2 = $this->createMock(GeneratorInterface::class);

        $registry = new GeneratorRegistry([
            'foo' => $builder1,
            'bar' => $builder2,
        ]);

        $builder = $registry->find('bar');
        self::assertSame($builder2, $builder);
    }
}

class_alias(GeneratorRegistryTest::class, 'Ibexa\Platform\Tests\Migration\Generator\CriterionGenerator\GeneratorRegistryTest');
