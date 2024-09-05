<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Form\Type\BlockAttribute;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

class AttributeUrlType extends AbstractType
{
    public function getParent()
    {
        return UrlType::class;
    }

    public function getBlockPrefix()
    {
        return 'block_configuration_attribute_url';
    }
}

class_alias(AttributeUrlType::class, 'EzSystems\EzPlatformPageFieldType\Form\Type\BlockAttribute\AttributeUrlType');
