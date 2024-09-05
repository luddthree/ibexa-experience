<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Type;

use Ibexa\FormBuilder\FieldType\Converter\FormConverter;
use Ibexa\FormBuilder\Form\Transformer\FormHashTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

class FormValueType extends AbstractType
{
    private FormConverter $converter;

    public function __construct(FormConverter $converter)
    {
        $this->converter = $converter;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new FormHashTransformer($this->converter));
    }

    public function getParent()
    {
        return HiddenType::class;
    }
}

class_alias(FormValueType::class, 'EzSystems\EzPlatformFormBuilder\Form\Type\FormValueType');
