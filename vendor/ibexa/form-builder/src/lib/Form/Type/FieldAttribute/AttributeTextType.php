<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Type\FieldAttribute;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AttributeTextType extends AbstractType
{
    /**
     * @return string|null
     */
    public function getParent()
    {
        return TextareaType::class;
    }

    /**
     * @return string
     */
    public function getFieldPrefix()
    {
        return 'field_configuration_attribute_text';
    }
}

class_alias(AttributeTextType::class, 'EzSystems\EzPlatformFormBuilder\Form\Type\FieldAttribute\AttributeTextType');
