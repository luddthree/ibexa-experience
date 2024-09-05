<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Measurement\NameSchema;

use Ibexa\Measurement\Value\Definition\GenericMeasurementType;
use Ibexa\Measurement\Value\Definition\GenericUnit;
use Ibexa\Measurement\Value\RangeValue;
use Ibexa\Measurement\Value\SimpleValue;
use Ibexa\ProductCatalog\Local\Repository\Attribute\AttributeType;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinition;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeGroup;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractStrategyTest extends TestCase
{
    public const ENG_GB = 'eng-GB';
    public const POL_PL = 'pol-PL';

    protected function getRangeValue(): RangeValue
    {
        $genericUnit = new GenericUnit('m', 'meter', true);

        return new RangeValue(
            new GenericMeasurementType(
                'length',
                ['centimeter' => $genericUnit],
                $genericUnit
            ),
            $genericUnit,
            0,
            1
        );
    }

    protected function getSimpleValue(): SimpleValue
    {
        $genericUnit = new GenericUnit('m', 'meter', true);

        return new SimpleValue(
            new GenericMeasurementType(
                'length',
                ['centimeter' => $genericUnit],
                $genericUnit
            ),
            $genericUnit,
            111
        );
    }

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

    public function getAttributeDefinitionRange(
        TranslatorInterface $translator,
        AttributeGroup $attributeGroup
    ): AttributeDefinition {
        return new AttributeDefinition(
            1,
            'foo',
            new AttributeType($translator, 'measurement_range'),
            $attributeGroup,
            'Foo',
            0,
            [self::ENG_GB, self::POL_PL],
            'Foo Definition',
            [self::ENG_GB => 'Foo eng', self::POL_PL => 'Foo pol'],
            [self::ENG_GB => 'Foo description eng', self::POL_PL => 'Foo description pol'],
        );
    }

    protected function getAttributeDefinitionSimple(
        TranslatorInterface $translator,
        AttributeGroup $attributeGroup
    ): AttributeDefinition {
        return new AttributeDefinition(
            1,
            'foo',
            new AttributeType($translator, 'measurement_single'),
            $attributeGroup,
            'Foo',
            0,
            [self::ENG_GB, self::POL_PL],
            'Foo Definition',
            [self::ENG_GB => 'Foo eng', self::POL_PL => 'Foo pol'],
            [self::ENG_GB => 'Foo description eng', self::POL_PL => 'Foo description pol'],
        );
    }
}
