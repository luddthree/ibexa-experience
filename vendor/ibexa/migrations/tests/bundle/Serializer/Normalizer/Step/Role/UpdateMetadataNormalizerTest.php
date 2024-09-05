<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Serializer\Normalizer\Step\Role;

use Ibexa\Bundle\Migration\Serializer\Normalizer\Step\Role\UpdateMetadataNormalizer;
use Ibexa\Migration\ValueObject\Step\Role\UpdateMetadata;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Bundle\Migration\Serializer\Normalizer\Step\Role\UpdateMetadataNormalizer
 */
final class UpdateMetadataNormalizerTest extends TestCase
{
    /**
     * @return array<mixed>
     */
    public function testNormalize(): array
    {
        $normalizer = new UpdateMetadataNormalizer();
        $normalized = $normalizer->normalize(new UpdateMetadata('__roleIdentifier__'));
        self::assertSame([
            'identifier' => '__roleIdentifier__',
        ], $normalized);

        return $normalized;
    }

    /**
     * @depends testNormalize
     *
     * @param array<mixed> $normalized
     */
    public function testDenormalize(array $normalized): void
    {
        $denormalizer = new UpdateMetadataNormalizer();
        self::assertEquals(
            new UpdateMetadata('__roleIdentifier__'),
            $denormalizer->denormalize($normalized, UpdateMetadata::class)
        );
    }

    public function testDenormalizeNullValue(): void
    {
        $denormalizer = new UpdateMetadataNormalizer();
        self::assertEquals(
            new UpdateMetadata(null),
            $denormalizer->denormalize(null, UpdateMetadata::class)
        );
    }
}

class_alias(UpdateMetadataNormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Serializer\Normalizer\Step\Role\UpdateMetadataNormalizerTest');
