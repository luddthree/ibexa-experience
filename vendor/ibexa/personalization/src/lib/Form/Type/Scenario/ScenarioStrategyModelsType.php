<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type\Scenario;

use Ibexa\Personalization\Form\Data\Scenario\ScenarioStrategyModelsData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ScenarioStrategyModelsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('first_model_strategy', ScenarioStrategyModelType::class, [
                'label' => false,
            ])
            ->add('second_model_strategy', ScenarioStrategyModelType::class, [
                'label' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ScenarioStrategyModelsData::class,
        ]);
    }
}

class_alias(ScenarioStrategyModelsType::class, 'Ibexa\Platform\Personalization\Form\Type\Scenario\ScenarioStrategyModelsType');
