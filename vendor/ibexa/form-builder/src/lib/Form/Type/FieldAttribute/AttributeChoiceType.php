<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Type\FieldAttribute;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class AttributeChoiceType extends AbstractType
{
    /**
     * @return string|null
     */
    public function getParent()
    {
        return HiddenType::class;
    }
}

class_alias(AttributeChoiceType::class, 'EzSystems\EzPlatformFormBuilder\Form\Type\FieldAttribute\AttributeChoiceType');
