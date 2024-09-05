<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type\Scenario;

use Ibexa\Personalization\Form\Data\Scenario\ScenarioRecommendSameCategoryPathData;
use Ibexa\Personalization\Form\DataTransformer\NegativeNumberTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ScenarioRecommendSameCategoryPathType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('checked', RadioType::class, [
                'label' => false,
                'required' => false,
            ])
            ->add('include_parent', CheckboxType::class, [
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

        $builder->get('subcategory_level')->addModelTransformer(new NegativeNumberTransformer());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ScenarioRecommendSameCategoryPathData::class,
        ]);
    }
}

class_alias(ScenarioRecommendSameCategoryPathType::class, 'Ibexa\Platform\Personalization\Form\Type\Scenario\ScenarioRecommendSameCategoryPathType');
