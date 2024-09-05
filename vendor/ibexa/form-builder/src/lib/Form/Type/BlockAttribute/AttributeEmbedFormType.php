<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Type\BlockAttribute;

use Ibexa\FieldTypePage\Form\Type\BlockAttribute\AttributeEmbedType;

class AttributeEmbedFormType extends AttributeEmbedType
{
    /**
     * @return string|null
     */
    public function getBlockPrefix(): string
    {
        return 'block_configuration_attribute_embed_form';
    }
}

class_alias(AttributeEmbedFormType::class, 'EzSystems\EzPlatformFormBuilder\Form\Type\BlockAttribute\AttributeEmbedFormType');
