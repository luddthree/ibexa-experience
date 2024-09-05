<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\DataMapper;

use Ibexa\Bundle\ProductCatalog\Form\Data\AttributeDefinitionCreateData;
use Ibexa\Bundle\ProductCatalog\Form\DataMapper\AttributeDefinitionCreateMapper;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeGroup;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeType;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Bundle\ProductCatalog\Form\DataMapper\AttributeDefinitionCreateMapper
 */
final class AttributeDefinitionCreateMapperTest extends TestCase
{
    public function testSuccessfulMap(): void
    {
        $data = new AttributeDefinitionCreateData();
        $data->setIdentifier('foo');
        $data->setName('Foo');
        $data->setDescription('Lorem Ipsum');
        $data->setAttributeGroup(new AttributeGroup(1, 'bar', 'Bar', 0, [], []));

        $language = $this->createMock(Language::class);
        $language->method('__get')->with('languageCode')->willReturn('eng-GB');

        $mapper = new AttributeDefinitionCreateMapper();
        $result = $mapper->mapToStruct(
            $data,
            $language,
            new AttributeType('integer')
        );
        self::assertSame('foo', $result->getIdentifier());
        self::assertSame(['eng-GB' => 'Foo'], $result->getNames());
        self::assertSame(['eng-GB' => 'Lorem Ipsum'], $result->getDescriptions());
        self::assertSame('bar', $result->getGroup()->getIdentifier());
        self::assertSame('integer', $result->getType()->getIdentifier());
    }
}
