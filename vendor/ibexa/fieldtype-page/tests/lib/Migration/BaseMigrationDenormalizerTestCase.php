<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\FieldTypePage\Migration;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * @internal
 */
abstract class BaseMigrationDenormalizerTestCase extends TestCase
{
    protected DenormalizerInterface $denormalizer;

    final protected function setUp(): void
    {
        $this->denormalizer = $this->buildDenormalizer();
    }

    abstract protected function buildDenormalizer(): DenormalizerInterface;

    public function testHasCacheableSupportsMethod(): void
    {
        self::assertInstanceOf(CacheableSupportsMethodInterface::class, $this->denormalizer);
        self::assertTrue($this->denormalizer->hasCacheableSupportsMethod());
    }
}
