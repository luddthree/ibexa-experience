<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\DataMapper;

use Ibexa\Bundle\ProductCatalog\Form\Data\CustomerGroupUpdateData;
use Ibexa\Bundle\ProductCatalog\Form\DataMapper\CustomerGroupUpdateMapper;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Bundle\ProductCatalog\Form\DataMapper\CustomerGroupUpdateMapper
 */
final class CustomerGroupUpdateMapperTest extends TestCase
{
    public function testMappingToStruct(): void
    {
        $data = new CustomerGroupUpdateData(
            1,
            $this->getTestLanguage(),
            'foo',
            'Foo',
            'Lorem Ipsum',
            '42',
        );

        $mapper = new CustomerGroupUpdateMapper();
        $result = $mapper->mapToStruct($data);
        self::assertSame(1, $result->getId());
        self::assertSame('foo', $result->getIdentifier());
        self::assertSame('Foo', $result->getName(2));
        self::assertSame('Lorem Ipsum', $result->getDescription(2));
        self::assertSame('42', $result->getGlobalPriceRate());
    }

    public function testMappingToStructWithEmptyValues(): void
    {
        $data = new CustomerGroupUpdateData(1, $this->getTestLanguage());
        $data->setName('Foo');

        $mapper = new CustomerGroupUpdateMapper();
        $result = $mapper->mapToStruct($data);
        self::assertSame(1, $result->getId());
        self::assertNull($result->getIdentifier());
        self::assertSame('Foo', $result->getName(2));
        self::assertSame('', $result->getDescription(2));
        self::assertNull($result->getGlobalPriceRate());
    }

    private function getTestLanguage(): Language
    {
        return new Language([
            'id' => 2,
            'languageCode' => 'eng-US',
        ]);
    }
}
