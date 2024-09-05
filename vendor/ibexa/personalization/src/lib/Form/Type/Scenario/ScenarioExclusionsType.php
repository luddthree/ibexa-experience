<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type\Scenario;

use Ibexa\Personalization\Event\Subscriber\Form\ScenarioExclusionsSubscriber;
use Ibexa\Personalization\Form\Data\Scenario\ScenarioExclusionsData;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ScenarioExclusionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('exclude_context_items_categories', CheckboxType::class, [
                'required' => false,
                'translation_domain' => 'ibexa_personalization',
                'label' => /** @Desc("Exclude category of the context item") */ 'scenario.exclusions.exclude_context_items_categories',
            ])
            ->add('exclude_categories', ScenarioExcludedCategoriesType::class, [
                'required' => false,
                'label' => false,
            ]);

        $builder->addEventSubscriber(new ScenarioExclusionsSubscriber());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ScenarioExclusionsData::class,
        ]);
    }
}
