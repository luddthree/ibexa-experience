<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\DataMapper;

use Ibexa\Bundle\ProductCatalog\Form\Data\CustomerGroupCreateData;
use Ibexa\Bundle\ProductCatalog\Form\DataMapper\CustomerGroupCreateMapper;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use PHPUnit\Framework\TestCase;

final class CustomerGroupCreateMapperTest extends TestCase
{
    public function testSuccessfulMap(): void
    {
        $data = new CustomerGroupCreateData();
        $data->setIdentifier('foo');
        $data->setName('Foo');
        $data->setDescription('Lorem Ipsum');
        $data->setGlobalPriceRate('42');
        $data->setLanguage($this->getTestLanguage());

        $mapper = new CustomerGroupCreateMapper();
        $result = $mapper->mapToStruct($data);
        self::assertSame('foo', $result->getIdentifier());
        self::assertSame('Foo', $result->getName(2));
        self::assertSame('Lorem Ipsum', $result->getDescription(2));
        self::assertSame('42', $result->getGlobalPriceRate());
    }

    private function getTestLanguage(): Language
    {
        return new Language([
            'id' => 2,
            'languageCode' => 'eng-US',
        ]);
    }
}
