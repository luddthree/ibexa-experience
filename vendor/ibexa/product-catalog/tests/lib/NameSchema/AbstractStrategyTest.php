<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\NameSchema;

use Ibexa\ProductCatalog\Local\Repository\Attribute\AttributeType;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinition;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeGroup;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractStrategyTest extends TestCase
{
    public const ENG_GB = 'eng-GB';
    public const POL_PL = 'pol-PL';

    protected function getAttributeGroup(): AttributeGroup
    {
        return new AttributeGroup(
            0,
            'Foo group',
            'Foo',
            0,
            [self::ENG_GB, self::POL_PL],
            [self::ENG_GB => 'Foo eng', self::POL_PL => 'Foo pol']
        );
    }

    protected function getAttributeDefinitionFoo(): AttributeDefinition
    {
        $attributeType = new AttributeType($this->getTranslator(), 'foo');

        return $this->getAttributeDefinition('foo', 'Foo', $attributeType);
    }

    /**
     * @param array<array<mixed>> $options
     */
    protected function getAttributeDefinition(
        string $identifier,
        string $name,
        AttributeType $attributeType,
        array $options = []
    ): AttributeDefinition {
        return new AttributeDefinition(
            1,
            $identifier,
            $attributeType,
            $this->getAttributeGroup(),
            $name,
            0,
            [self::ENG_GB, self::POL_PL],
            $name . ' Definition',
            [self::ENG_GB => $name . ' eng', self::POL_PL => $name . ' pol'],
            [self::ENG_GB => $name . ' description eng', self::POL_PL => $name . ' description pol'],
            $options
        );
    }

    protected function getTranslator(): TranslatorInterface
    {
        return $this->createMock(TranslatorInterface::class);
    }
}
