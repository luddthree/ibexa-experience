<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type\Scenario;

use Ibexa\Personalization\Form\Data\Scenario\ScenarioRecommendMainCategoryAndSubcategoriesData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ScenarioRecommendMainCategoryAndSubcategoriesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('checked', RadioType::class, [
                'label' => false,
                'required' => false,
            ])
            ->add('subcategory_level', IntegerType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'min' => 1,
                    'max' => 5,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ScenarioRecommendMainCategoryAndSubcategoriesData::class,
        ]);
    }
}

class_alias(ScenarioRecommendMainCategoryAndSubcategoriesType::class, 'Ibexa\Platform\Personalization\Form\Type\Scenario\ScenarioRecommendMainCategoryAndSubcategoriesType');
