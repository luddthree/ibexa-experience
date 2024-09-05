<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Serializer\Denormalizer;

use Ibexa\Bundle\Migration\Serializer\Denormalizer\StepsDenormalizer;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use PHPUnit\Framework\MockObject\Stub\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * @covers \Ibexa\Bundle\Migration\Serializer\Denormalizer\StepsDenormalizer
 */
final class StepsDenormalizerTest extends TestCase
{
    public function testDenormalize(): void
    {
        $context = [
            'discard_invalid_steps' => true,
        ];
        $denormalizer = new StepsDenormalizer();
        $subdenormalizer = $this->createMock(DenormalizerInterface::class);
        $subdenormalizer
            ->method('denormalize')
            ->withConsecutive(
                ['valid step'],
                ['valid step'],
                ['invalid step'],
                ['valid step'],
            )
            ->willReturnOnConsecutiveCalls(
                'valid step',
                'valid step',
                new Exception(new \RuntimeException('invalid step')),
                'valid step',
            );

        $denormalizer->setDenormalizer($subdenormalizer);

        /** @var \Traversable<string> $result */
        $result = $denormalizer->denormalize([
            'valid step',
            'valid step',
            'invalid step',
            'valid step',
        ], StepInterface::class . '[]', 'any', $context);

        $this->assertSame([
            'valid step',
            'valid step',
            'valid step',
        ], iterator_to_array($result));
    }
}

class_alias(StepsDenormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Serializer\Denormalizer\StepsDenormalizerTest');
