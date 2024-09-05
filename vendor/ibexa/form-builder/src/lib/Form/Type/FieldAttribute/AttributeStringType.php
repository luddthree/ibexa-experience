<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Type\FieldAttribute;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AttributeStringType extends AbstractType
{
    /**
     * @return string|null
     */
    public function getParent()
    {
        return TextType::class;
    }

    /**
     * @return string
     */
    public function getFieldPrefix()
    {
        return 'field_configuration_attribute_string';
    }
}

class_alias(AttributeStringType::class, 'EzSystems\EzPlatformFormBuilder\Form\Type\FieldAttribute\AttributeStringType');
