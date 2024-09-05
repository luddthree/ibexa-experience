<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Personalization\Form\Data\RecommendationCallData;
use Ibexa\Personalization\Value\Scenario\Scenario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class RecommendationCallType extends AbstractType
{
    private ConfigResolverInterface $configResolver;

    public function __construct(ConfigResolverInterface $configResolver)
    {
        $this->configResolver = $configResolver;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var \Ibexa\Personalization\Value\Scenario\Scenario $scenario */
        $scenario = $builder->getOption('scenario');
        $outputTypes = $scenario->getOutputItemTypes();

        $builder
            ->add('user_id', TextType::class, [
                'required' => false,
                'empty_data' => $this->configResolver->getParameter('personalization.recommendations.user_id'),
            ])
            ->add('no_of_recommendations', IntegerType::class, [
                'required' => true,
                'empty_data' => $this->configResolver->getParameter('personalization.recommendations.limit'),
                'attr' => [
                    'min' => $this->configResolver->getParameter('personalization.recommendations.min_value'),
                    'max' => $this->configResolver->getParameter('personalization.recommendations.max_value'),
                ],
            ])
            ->add('output_type', ItemTypeChoiceType::class, [
                'required' => true,
                'choices' => $outputTypes,
            ])
            ->add('category_path_filter', TextType::class, [
                'required' => false,
            ])
            ->add('context_items', ContextItemsType::class, [
                'required' => false,
            ])
            ->add('attributes', CollectionType::class, [
                'entry_type' => TextType::class,
                'allow_add' => true,
                'delete_empty' => true,
            ])
            ->add('custom_parameters', CollectionType::class, [
                'entry_type' => RecommendationCallCustomParametersType::class,
                'allow_add' => true,
                'delete_empty' => true,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
                'data_class' => RecommendationCallData::class,
                'scenario' => null,
        ]);
        $resolver->setAllowedTypes('scenario', [Scenario::class, 'null']);
    }
}

class_alias(RecommendationCallType::class, 'Ibexa\Platform\Personalization\Form\Type\RecommendationCallType');
