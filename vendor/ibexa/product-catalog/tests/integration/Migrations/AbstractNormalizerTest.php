<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Migrations;

use Ibexa\Tests\Integration\ProductCatalog\IbexaKernelTestCase;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Abstract class for normalizer-related tests.
 *
 * Ensures implicitly that relevant normalizers are registered in Ibexa Migration's serializer.
 *
 * @template T of object
 */
abstract class AbstractNormalizerTest extends IbexaKernelTestCase
{
    protected SerializerInterface $serializer;

    protected function setUp(): void
    {
        $this->serializer = self::getMigrationSerializer();
    }

    /**
     * @return class-string<T>
     */
    abstract protected static function getHandledClass(): string;

    /**
     * @param T $step
     *
     * @dataProvider provideForSerialization
     */
    final public function testSerialization(object $step, string $expected): void
    {
        self::assertInstanceOf($this->getHandledClass(), $step);
        $yaml = $this->serializer->serialize($step, 'yaml');
        self::assertSame($expected, $yaml);
    }

    /**
     * @phpstan-return iterable<
     *     array{
     *         T,
     *         string,
     *     },
     * >
     */
    abstract public function provideForSerialization(): iterable;

    /**
     * @dataProvider provideForDeserialization
     *
     * @param string $yaml
     * @param callable(object $deserialized): void $expectation
     */
    final public function testDeserialization(string $yaml, callable $expectation): void
    {
        $step = $this->serializer->deserialize($yaml, $this->getHandledClass(), 'yaml');
        $expectation($step);
    }

    /**
     * @phpstan-return iterable<
     *     array{
     *         string,
     *         callable(object $deserialized): void,
     *     },
     * >
     */
    abstract public function provideForDeserialization(): iterable;
}
