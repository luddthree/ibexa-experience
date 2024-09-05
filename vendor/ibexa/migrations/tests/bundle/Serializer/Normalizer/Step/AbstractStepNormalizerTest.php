<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Serializer\Normalizer\Step;

use Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Contracts\Migration\Serializer\AbstractStepNormalizer
 */
final class AbstractStepNormalizerTest extends TestCase
{
    public function testNormalizeStepIsForbiddenFromReturningType(): void
    {
        $mock = $this->createConfiguredMock(AbstractStepNormalizer::class, [
            'normalizeStep' => ['type' => 'foo'],
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $expectedMessage = '~^Expected "type" to not be set by [a-zA-Z_0-9]+::normalizeStep method.$~';
        $this->expectExceptionMessageMatches($expectedMessage);
        $mock->normalize($this->createMock(StepInterface::class));
    }

    public function testNormalizeStepIsForbiddenFromReturningMode(): void
    {
        $mock = $this->createConfiguredMock(AbstractStepNormalizer::class, [
            'normalizeStep' => ['mode' => 'foo'],
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $expectedMessage = '~^Expected "mode" to not be set by [a-zA-Z_0-9]+::normalizeStep method.$~';
        $this->expectExceptionMessageMatches($expectedMessage);
        $mock->normalize($this->createMock(StepInterface::class));
    }

    public function testNormalizeForEmptyNormalizeStep(): void
    {
        $mock = $this->createConfiguredMock(AbstractStepNormalizer::class, [
            'normalizeStep' => [],
            'getMode' => 'foo',
            'getType' => 'bar',
        ]);

        $normalized = $mock->normalize($this->createMock(StepInterface::class));
        self::assertSame([
            'type' => 'bar',
            'mode' => 'foo',
        ], $normalized);
    }

    public function testNormalizeMergesNormalizeStep(): void
    {
        $mock = $this->createConfiguredMock(AbstractStepNormalizer::class, [
            'normalizeStep' => [
                'answer to life, universe and everything' => [42],
            ],
            'getMode' => 'foo',
            'getType' => 'bar',
        ]);

        $normalized = $mock->normalize($this->createMock(StepInterface::class));
        self::assertSame([
            'type' => 'bar',
            'mode' => 'foo',
            'answer to life, universe and everything' => [42],
        ], $normalized);
    }

    public function testNormalizePassesStepClassIntoNormalizeStepContext(): void
    {
        $mock = $this->createConfiguredMock(AbstractStepNormalizer::class, [
            'getMode' => 'foo',
            'getType' => 'bar',
            'getHandledClassType' => 'foo_class',
        ]);
        $mock->expects(self::once())
            ->method('normalizeStep')
            ->with(
                self::isInstanceOf(StepInterface::class),
                null,
                [
                    'step_class' => 'foo_class',
                ],
            );

        $mock->normalize($this->createMock(StepInterface::class), null, [
            'step_class' => 'will_be_overwritten',
        ]);
    }

    public function testDenormalizePassesStepClassIntoDenormalizeStepContext(): void
    {
        $mock = $this->createConfiguredMock(AbstractStepNormalizer::class, [
            'getMode' => 'foo',
            'getType' => 'bar',
            'getHandledClassType' => 'foo_class',
        ]);
        $mock->expects(self::once())
            ->method('denormalizeStep')
            ->with(
                self::identicalTo([]),
                StepInterface::class,
                null,
                [
                    'step_class' => 'foo_class',
                ],
            )
            ->willReturn($this->createMock(StepInterface::class));

        $mock->denormalize([], StepInterface::class, null, [
            'step_class' => 'will_be_overwritten',
        ]);
    }
}

class_alias(AbstractStepNormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Serializer\Normalizer\Step\AbstractStepNormalizerTest');
