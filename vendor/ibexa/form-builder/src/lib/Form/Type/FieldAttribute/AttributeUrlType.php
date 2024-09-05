<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Type\FieldAttribute;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

class AttributeUrlType extends AbstractType
{
    /**
     * @return string|null
     */
    public function getParent()
    {
        return UrlType::class;
    }

    /**
     * @return string
     */
    public function getFieldPrefix()
    {
        return 'field_configuration_attribute_url';
    }
}

class_alias(AttributeUrlType::class, 'EzSystems\EzPlatformFormBuilder\Form\Type\FieldAttribute\AttributeUrlType');
