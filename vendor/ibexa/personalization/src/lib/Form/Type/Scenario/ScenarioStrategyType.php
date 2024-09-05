<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type\Scenario;

use Ibexa\Personalization\Event\Subscriber\Form\ScenarioStrategySubscriber;
use Ibexa\Personalization\Form\Data\Scenario\ScenarioStrategyData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ScenarioStrategyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('category_path', ScenarioCategoryPathType::class, [
                'required' => false,
                'action_type' => $builder->getOption('action_type'),
            ])
            ->add('models', ScenarioStrategyModelsType::class);

        $builder->addEventSubscriber(new ScenarioStrategySubscriber());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ScenarioStrategyData::class,
            'action_type' => null,
        ])
        ->setAllowedTypes('action_type', 'string');
    }
}

class_alias(ScenarioStrategyType::class, 'Ibexa\Platform\Personalization\Form\Type\Scenario\ScenarioStrategyType');
