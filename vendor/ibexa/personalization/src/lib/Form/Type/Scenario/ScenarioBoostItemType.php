<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type\Scenario;

use Ibexa\Personalization\Form\Data\Scenario\ScenarioBoostItemData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ScenarioBoostItemType extends AbstractType
{
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('enabled', CheckboxType::class, [
                'label' => $this->translator->trans(/** @Desc("Boost item, if a user has an attribute with the same value") */
                    'scenario.user_profile_settings.boost_item',
                    [],
                    'ibexa_personalization'
                ),
                'attr' => [
                    'data-related-id' => $builder->getOption('data-related-id'),
                ],
            ])
            ->add('attribute', TextType::class, [
                'required' => true,
            ])
            ->add('position', IntegerType::class, [
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ScenarioBoostItemData::class,
            'data-related-id' => null,
        ])
        ->setAllowedTypes('data-related-id', ['string', 'null']);
    }
}

class_alias(ScenarioBoostItemType::class, 'Ibexa\Platform\Personalization\Form\Type\Scenario\ScenarioBoostItemType');
