<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type\BlockAttribute;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @internal
 */
final class CustomerSelectAttributeType extends AbstractType
{
    private ChoiceLoaderInterface $choiceLoader;

    public function __construct(ChoiceLoaderInterface $choiceLoader)
    {
        $this->choiceLoader = $choiceLoader;
    }

    public function getParent(): ?string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): ?string
    {
        return 'personalization_customer_select';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'choice_loader' => ChoiceList::lazy($this, fn () => $this->choiceLoader),
            ]
        );
    }
}
