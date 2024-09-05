<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Type\Field;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\SubmitButtonTypeInterface;

class ButtonFieldType extends AbstractFieldType implements SubmitButtonTypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return SubmitType::class;
    }
}

class_alias(ButtonFieldType::class, 'EzSystems\EzPlatformFormBuilder\Form\Type\Field\ButtonFieldType');
