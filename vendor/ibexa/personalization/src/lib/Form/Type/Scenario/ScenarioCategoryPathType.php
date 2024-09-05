<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type\Scenario;

use Ibexa\Personalization\Form\Data\Scenario\ScenarioCategoryPathData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ScenarioCategoryPathType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $defaultAttr = [];

        if (ScenarioType::CREATE_ACTION === $builder->getOption('action_type')) {
            $defaultAttr = [
                'checked' => true,
            ];
        }

        $builder
            ->add('whole_site', RadioType::class, [
                'label' => false,
                'required' => false,
                'attr' => $defaultAttr,
            ])
            ->add('same_category', ScenarioRecommendSameCategoryPathType::class, [
                'label' => false,
                'required' => false,
            ])
            ->add('main_category_and_subcategories', ScenarioRecommendMainCategoryAndSubcategoriesType::class, [
                'label' => false,
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ScenarioCategoryPathData::class,
            'action_type' => null,
        ])
        ->setAllowedTypes('action_type', 'string');
    }
}

class_alias(ScenarioCategoryPathType::class, 'Ibexa\Platform\Personalization\Form\Type\Scenario\ScenarioCategoryPathType');
