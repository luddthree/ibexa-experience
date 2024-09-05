<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Form\Type;

use Ibexa\Segmentation\Form\ChoiceLoader\SegmentGroupChoiceLoader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SegmentGroupChoiceType extends AbstractType
{
    /** @var \Ibexa\Segmentation\Form\ChoiceLoader\SegmentGroupChoiceLoader */
    private $choiceLoader;

    public function __construct(SegmentGroupChoiceLoader $choiceLoader)
    {
        $this->choiceLoader = $choiceLoader;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'multiple' => true,
            'required' => false,
            'choice_loader' => $this->choiceLoader,
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}

class_alias(SegmentGroupChoiceType::class, 'Ibexa\Platform\Segmentation\Form\Type\SegmentGroupChoiceType');
