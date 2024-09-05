<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Form\Type\BlockAttribute;

class AttributeEmbedVideoType extends AttributeEmbedType
{
    public function getBlockPrefix(): string
    {
        return 'block_configuration_attribute_embed_video';
    }
}

class_alias(AttributeEmbedVideoType::class, 'EzSystems\EzPlatformPageFieldType\Form\Type\BlockAttribute\AttributeEmbedVideoType');
