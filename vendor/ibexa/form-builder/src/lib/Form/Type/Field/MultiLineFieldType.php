<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Type\Field;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class MultiLineFieldType extends AbstractFieldType
{
    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return TextareaType::class;
    }
}

class_alias(MultiLineFieldType::class, 'EzSystems\EzPlatformFormBuilder\Form\Type\Field\MultiLineFieldType');
