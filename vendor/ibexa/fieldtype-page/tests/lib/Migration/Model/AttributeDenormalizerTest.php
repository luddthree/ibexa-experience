<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\FieldTypePage\Migration\Model;

use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Attribute;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\FieldTypePage\Migration\Model\AttributeDenormalizer;
use Ibexa\Tests\FieldTypePage\Migration\BaseMigrationDenormalizerTestCase;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class AttributeDenormalizerTest extends BaseMigrationDenormalizerTestCase
{
    protected function buildDenormalizer(): DenormalizerInterface
    {
        return new AttributeDenormalizer();
    }

    public function testSupportsDenormalization(): void
    {
        self::assertTrue($this->denormalizer->supportsDenormalization([], Attribute::class));
        self::assertFalse($this->denormalizer->supportsDenormalization([], BlockValue::class));
    }

    /**
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function testDenormalize(): void
    {
        self::assertEquals(
            new Attribute(
                'a-2a196ea0-7f1e-4040-b649-640df05f99c1',
                'my_attribute',
                'my value'
            ),
            $this->denormalizer->denormalize(
                [
                    'id' => 'a-2a196ea0-7f1e-4040-b649-640df05f99c1',
                    'name' => 'my_attribute',
                    'value' => 'my value',
                ],
                Attribute::class
            )
        );
    }

    /**
     * @return iterable<string, array{mixed}>
     */
    public function getInvalidData(): iterable
    {
        yield 'null' => [null];
        yield 'missing fields' => [[]];
        yield 'missing value' => [['id' => '123', 'name' => 'Name']];
        yield 'missing name' => [['id' => '123', 'value' => 'value']];
        yield 'missing id' => [['name' => 'Name', 'value' => 'value']];
        yield 'null id' => [['id' => null, 'name' => 'Name', 'value' => 'value']];
        yield 'null name' => [['id' => '123', 'name' => null, 'value' => 'value']];
        yield 'null value' => [['id' => '123', 'name' => null, 'value' => null]];
    }

    /**
     * @dataProvider getInvalidData
     *
     * @param mixed $data
     *
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function testDenormalizeThrowsInvalidArgumentException($data): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->denormalizer->denormalize($data, Attribute::class);
    }
}
