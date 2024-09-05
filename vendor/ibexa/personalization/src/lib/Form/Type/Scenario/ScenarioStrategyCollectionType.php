<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type\Scenario;

use Ibexa\Personalization\Form\Data\Scenario\ScenarioStrategyCollectionData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ScenarioStrategyCollectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('primary_models', ScenarioStrategyType::class, [
                'label' => false,
                'action_type' => $builder->getOption('action_type'),
            ])
            ->add('fallback', ScenarioStrategyType::class, [
                'label' => false,
                'action_type' => $builder->getOption('action_type'),
            ])
            ->add('fail_safe', ScenarioStrategyType::class, [
                'label' => false,
                'action_type' => $builder->getOption('action_type'),
            ])
            ->add('ultimately_fail_safe', ScenarioStrategyType::class, [
                'label' => false,
                'action_type' => $builder->getOption('action_type'),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ScenarioStrategyCollectionData::class,
            'action_type' => null,
        ])
        ->setAllowedTypes('action_type', 'string');
    }
}

class_alias(ScenarioStrategyCollectionType::class, 'Ibexa\Platform\Personalization\Form\Type\Scenario\ScenarioStrategyCollectionType');
