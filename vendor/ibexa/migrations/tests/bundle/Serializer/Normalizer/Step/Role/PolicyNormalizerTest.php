<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Serializer\Normalizer\Step\Role;

use Ibexa\Bundle\Migration\Serializer\Normalizer\Step\Role\PolicyNormalizer;
use Ibexa\Migration\ValueObject\Step\Role\Limitation;
use Ibexa\Migration\ValueObject\Step\Role\Policy;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @covers \Ibexa\Bundle\Migration\Serializer\Normalizer\Step\Role\PolicyNormalizer
 */
final class PolicyNormalizerTest extends TestCase
{
    /**
     * @return array{
     *     module: string,
     *     function: string,
     *     limitations: string[],
     * }
     */
    public function testNormalize(): array
    {
        $limitation = new Limitation('__limitation__', []);

        $delegatedNormalizer = $this->createMock(NormalizerInterface::class);
        $delegatedNormalizer->expects(self::atLeastOnce())
            ->method('normalize')
            ->with([$limitation])
            ->willReturn([
                '__delegated_normalized_result__',
            ]);
        $normalizer = new PolicyNormalizer();
        $normalizer->setNormalizer($delegatedNormalizer);

        $policy = new Policy('__module__', '__function__', [
            $limitation,
        ]);
        self::assertTrue($normalizer->supportsNormalization($policy));
        $normalized = $normalizer->normalize($policy);

        self::assertSame([
            'module' => '__module__',
            'function' => '__function__',
            'limitations' => ['__delegated_normalized_result__'],
        ], $normalized);

        return $normalized;
    }

    /**
     * @depends testNormalize
     *
     * @param array{
     *     module: string,
     *     function: string,
     *     limitations: string[],
     * } $normalized
     */
    public function testDenormalize(array $normalized): void
    {
        $limitation = new Limitation('__limitation__', []);

        $expectedPolicy = new Policy('__module__', '__function__', [$limitation]);

        $delegatedDenormalizer = $this->createMock(DenormalizerInterface::class);
        $delegatedDenormalizer->expects(self::atLeastOnce())
            ->method('denormalize')
            ->with(
                [
                    '__delegated_normalized_result__',
                ],
                'Ibexa\Migration\ValueObject\Step\Role\Limitation[]'
            )
            ->willReturn([$limitation]);
        $denormalizer = new PolicyNormalizer();
        $denormalizer->setDenormalizer($delegatedDenormalizer);

        self::assertTrue($denormalizer->supportsDenormalization($normalized, Policy::class));
        self::assertEquals(
            $expectedPolicy,
            $denormalizer->denormalize($normalized, Policy::class)
        );
    }
}

class_alias(PolicyNormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Serializer\Normalizer\Step\Role\PolicyNormalizerTest');
