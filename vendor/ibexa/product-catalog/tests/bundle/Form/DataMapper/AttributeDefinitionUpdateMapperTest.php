<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\DataMapper;

use Ibexa\Bundle\ProductCatalog\Form\Data\AttributeDefinitionUpdateData;
use Ibexa\Bundle\ProductCatalog\Form\DataMapper\AttributeDefinitionUpdateMapper;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeGroup;
use PHPUnit\Framework\TestCase;

final class AttributeDefinitionUpdateMapperTest extends TestCase
{
    public function testSuccessfulMap(): void
    {
        $data = new AttributeDefinitionUpdateData();
        $data->setIdentifier('foo');
        $data->setName('Foo');
        $data->setDescription('Lorem Ipsum');
        $data->setAttributeGroup(new AttributeGroup(1, 'bar', 'Bar', 0, [], []));
        $data->setOriginalIdentifier('baz');

        $language = $this->createMock(Language::class);
        $language->method('__get')
            ->with('languageCode')
            ->willReturn('eng-GB');

        $mapper = new AttributeDefinitionUpdateMapper();
        $result = $mapper->mapToStruct(
            $data,
            $language,
        );
        self::assertSame('foo', $result->getIdentifier());
        self::assertSame(['eng-GB' => 'Foo'], $result->getNames());
        self::assertSame(['eng-GB' => 'Lorem Ipsum'], $result->getDescriptions());
        $attributeGroup = $result->getGroup();
        self::assertInstanceOf(AttributeGroup::class, $attributeGroup);
        self::assertSame('bar', $attributeGroup->getIdentifier());
    }
}
