<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Serializer\Denormalizer;

use Ibexa\Bundle\Migration\Serializer\Denormalizer\ReferenceResolutionDenormalizer;
use Ibexa\Migration\Reference\CollectorInterface;
use Ibexa\Migration\ValueObject\Reference\Collection;
use Ibexa\Migration\ValueObject\Reference\Reference;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * @covers \Ibexa\Bundle\Migration\Serializer\Denormalizer\ReferenceResolutionDenormalizer
 */
final class ReferenceResolutionDenormalizerTest extends TestCase
{
    /** @var \Ibexa\Migration\Reference\CollectorInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $collector;

    /** @var \Ibexa\Bundle\Migration\Serializer\Denormalizer\ReferenceResolutionDenormalizer */
    private $denormalizer;

    protected function setUp(): void
    {
        $this->collector = $this->createMock(CollectorInterface::class);

        $this->denormalizer = new ReferenceResolutionDenormalizer($this->collector);

        $subdenormalizer = $this->createMock(DenormalizerInterface::class);
        $subdenormalizer->method('denormalize')->willReturnArgument(0);

        $this->denormalizer->setDenormalizer($subdenormalizer);
    }

    public function testIgnoresNonReferenceValues(): void
    {
        $data = [
            [
                'foo' => 'bar',
                0 => [
                    'foo' => 'bar',
                ],
            ],
            'foo' => 1,
            'bar' => 42,
        ];
        $result = $this->denormalizer->denormalize($data, StepInterface::class);

        self::assertSame($data, $result);
    }

    public function testChangesReferenceValues(): void
    {
        $this->collector->method('getCollection')->willReturn(new Collection([
            Reference::create('__reference_name__', '__reference_value__'),
            Reference::create('__reference_name:with_colon__', '__reference_value:with_colon__'),
            Reference::create('__reference_name_int__', 42),
        ]));

        $inputData = [
            [
                'foo' => 'reference:__reference_name__',
                0 => [
                    'foo' => 'reference:__reference_name:with_colon__',
                ],
            ],
            'foo' => 42,
            'bar' => 'reference:__reference_name_int__',
        ];
        $expectedData = [
            [
                'foo' => '__reference_value__',
                0 => [
                    'foo' => '__reference_value:with_colon__',
                ],
            ],
            'foo' => 42,
            'bar' => 42,
        ];

        $result = $this->denormalizer->denormalize($inputData, StepInterface::class);

        self::assertSame($expectedData, $result);
    }

    public function testSupportsAllSteps(): void
    {
        self::assertFalse($this->denormalizer->supportsDenormalization([], \stdClass::class));
        self::assertFalse(
            $this->denormalizer->supportsDenormalization([], StepInterface::class),
            sprintf(
                '%s should not handle %s, only subclasses',
                ReferenceResolutionDenormalizer::class,
                StepInterface::class,
            ),
        );
        self::assertTrue($this->denormalizer->supportsDenormalization([], DummyStep::class));
        self::assertTrue($this->denormalizer->supportsDenormalization([], DummyStep::class, null, [
            'references_resolved' => false,
        ]));
        self::assertFalse($this->denormalizer->supportsDenormalization([], DummyStep::class, null, [
            'references_resolved' => true,
        ]));
    }
}
