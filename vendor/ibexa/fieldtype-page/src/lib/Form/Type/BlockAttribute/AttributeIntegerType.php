<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Form\Type\BlockAttribute;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class AttributeIntegerType extends AbstractType
{
    public function getParent()
    {
        return IntegerType::class;
    }

    public function getBlockPrefix()
    {
        return 'block_configuration_attribute_integer';
    }
}

class_alias(AttributeIntegerType::class, 'EzSystems\EzPlatformPageFieldType\Form\Type\BlockAttribute\AttributeIntegerType');
