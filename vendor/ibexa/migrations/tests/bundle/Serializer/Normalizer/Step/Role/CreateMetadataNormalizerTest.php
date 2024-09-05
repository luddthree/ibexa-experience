<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Serializer\Normalizer\Step\Role;

use Ibexa\Bundle\Migration\Serializer\Normalizer\Step\Role\CreateMetadataNormalizer;
use Ibexa\Migration\ValueObject\Step\Role\CreateMetadata;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Bundle\Migration\Serializer\Normalizer\Step\Role\CreateMetadataNormalizer
 */
final class CreateMetadataNormalizerTest extends TestCase
{
    /**
     * @return array<mixed>
     */
    public function testNormalize(): array
    {
        $normalizer = new CreateMetadataNormalizer();
        $normalized = $normalizer->normalize(new CreateMetadata('__roleIdentifier__'));
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
        $denormalizer = new CreateMetadataNormalizer();
        self::assertEquals(
            new CreateMetadata('__roleIdentifier__'),
            $denormalizer->denormalize($normalized, CreateMetadata::class)
        );
    }
}

class_alias(CreateMetadataNormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Serializer\Normalizer\Step\Role\CreateMetadataNormalizerTest');
