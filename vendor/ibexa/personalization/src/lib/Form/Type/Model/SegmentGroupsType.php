<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type\Model;

use Ibexa\Personalization\Form\DataTransformer\SegmentItemGroupsTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class SegmentGroupsType extends AbstractType
{
    public function getBlockPrefix(): string
    {
        return 'personalization_segment_groups';
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new SegmentItemGroupsTransformer());
    }

    public function getParent()
    {
        return TextType::class;
    }
}
