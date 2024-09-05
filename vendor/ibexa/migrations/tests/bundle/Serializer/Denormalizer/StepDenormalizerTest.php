<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Serializer\Denormalizer;

use Ibexa\Bundle\Migration\Serializer\Denormalizer\StepDenormalizer;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Tests\Bundle\Migration\IbexaKernelTestCase;
use InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * @covers \Ibexa\Bundle\Migration\Serializer\Denormalizer\StepDenormalizer
 */
class StepDenormalizerTest extends IbexaKernelTestCase
{
    /**
     * @dataProvider provideForExceptionTests
     *
     * @param class-string<\Exception> $expectedException
     * @param array<mixed> $data
     */
    public function testDenormalize($data, string $expectedException, string $expectedExceptionMsg): void
    {
        $denormalizer = new StepDenormalizer([]);
        $subDenormalizer = $this->createMock(DenormalizerInterface::class);
        $denormalizer->setDenormalizer($subDenormalizer);

        $this->expectException($expectedException);
        $this->expectExceptionMessage($expectedExceptionMsg);
        $denormalizer->denormalize(
            $data,
            StepInterface::class,
        );
    }

    /**
     * @return iterable<array{mixed, class-string<\Exception>, string}>
     */
    public function provideForExceptionTests(): iterable
    {
        yield [
            null,
            InvalidArgumentException::class,
            'Expected an array. Got: NULL',
        ];

        yield [
            [],
            InvalidArgumentException::class,
            'Step `type` key is missing.',
        ];

        yield [
            [
                'type' => 'foo',
            ],
            InvalidArgumentException::class,
            'Step `mode` key is missing.',
        ];

        yield [
            [
                'type' => 'foo',
                'mode' => 'bar',
            ],
            InvalidArgumentException::class,
            'Unknown step',
        ];
    }

    /**
     * @dataProvider provideValidTypes
     *
     * @param array{type: string, mode: string} $types
     */
    public function testDenormalizationForValidTypes(array $types): void
    {
        self::bootKernel();
        $denormalizer = self::getServiceByClassName(StepDenormalizer::class);
        $mockedDenormalizer = $this->createMock(DenormalizerInterface::class);
        $expectedStep = $this->createMock(StepInterface::class);
        $mockedDenormalizer
            ->expects(self::once())
            ->method('denormalize')
            ->with($types)
            ->willReturn($expectedStep);
        $denormalizer->setDenormalizer($mockedDenormalizer);

        $result = $denormalizer->denormalize(
            $types,
            StepInterface::class
        );

        self::assertSame($expectedStep, $result);
    }

    /**
     * @return iterable<array{array{type: string, mode: string}}>
     */
    public function provideValidTypes(): iterable
    {
        yield [
            [
                'type' => 'content',
                'mode' => 'create',
            ],
        ];

        yield [
            [
                'type' => 'content_type',
                'mode' => 'create',
            ],
        ];

        yield [
            [
                'type' => 'content_type_group',
                'mode' => 'create',
            ],
        ];
    }
}

class_alias(StepDenormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Serializer\Denormalizer\StepDenormalizerTest');
