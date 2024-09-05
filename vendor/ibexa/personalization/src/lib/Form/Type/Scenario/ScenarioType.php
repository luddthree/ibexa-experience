<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type\Scenario;

use Ibexa\Personalization\Form\Data\Scenario\ScenarioData;
use Ibexa\Personalization\Form\Type\ItemTypeChoiceType;
use Ibexa\Personalization\Form\Type\ItemTypeListType;
use Ibexa\Personalization\Validator\Constraints\UniqueScenarioName;
use Ibexa\Personalization\Validator\Constraints\UniqueScenarioReferenceCode;
use Ibexa\Personalization\Value\Content\ItemTypeList;
use Ibexa\Personalization\Value\Model\ModelList;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ScenarioType extends AbstractType
{
    public const BTN_SAVE_AND_CLOSE = 'save_and_close';
    public const BTN_SAVE = 'save';
    public const CREATE_ACTION = 'create';
    public const EDIT_ACTION = 'edit';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $itemTypeList = $builder->getOption('item_type_list');
        $customerId = $builder->getOption('customer_id');
        $actionType = $builder->getOption('action_type');
        $referenceCodeConstraints = self::CREATE_ACTION === $actionType
            ? [new UniqueScenarioReferenceCode(['customerId' => $customerId])]
            : [];

        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'constraints' => [new UniqueScenarioName(
                    [
                        'customerId' => $customerId,
                        'actionType' => $actionType,
                        'referenceCode' => self::EDIT_ACTION === $actionType ? $builder->getData()->getId() : null,
                    ]
                )],
            ])
            ->add('id', TextType::class, [
                'required' => true,
                'constraints' => $referenceCodeConstraints,
                'disabled' => self::EDIT_ACTION === $builder->getOption('action_type'),
            ])
            ->add('input_type', ItemTypeChoiceType::class, [
                'required' => true,
                'choices' => $itemTypeList,
                'choice_value' => 'id',
            ])
            ->add('output_type', ItemTypeListType::class, [
                'required' => true,
                'multiple' => true,
                'choices' => $itemTypeList,
                'choice_value' => 'id',
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
            ])
            ->add('user_profile_settings', ScenarioUserProfileSettingsType::class, [
                'required' => false,
            ])
            ->add('exclusions', ScenarioExclusionsType::class, [
                'required' => false,
            ])
            ->add('strategy', ScenarioStrategyCollectionType::class, [
                'label' => false,
                'action_type' => $actionType,
            ])
            ->add(self::BTN_SAVE_AND_CLOSE, SubmitType::class, [
                'attr' => [
                    'class' => 'd-none',
                ],
            ])
            ->add(self::BTN_SAVE, SubmitType::class, [
                'attr' => [
                    'class' => 'd-none',
                ],
           ]);

        if ($builder->getOption('is_commerce')) {
            $builder->add('commerce_settings', ScenarioCommerceSettingsType::class, [
                'required' => false,
                'is_variant_supported' => $builder->getOption('is_variant_supported'),
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => ScenarioData::class,
                'customer_id' => null,
                'item_type_list' => null,
                'is_commerce' => null,
                'is_variant_supported' => null,
                'action_type' => null,
                'model_list' => null,
            ])
            ->setAllowedTypes('customer_id', 'int')
            ->setAllowedTypes('item_type_list', ItemTypeList::class)
            ->setAllowedTypes('is_commerce', 'bool')
            ->setAllowedTypes('is_variant_supported', 'bool')
            ->setAllowedTypes('action_type', 'string')
            ->setAllowedTypes('model_list', ModelList::class);
    }
}

class_alias(ScenarioType::class, 'Ibexa\Platform\Personalization\Form\Type\Scenario\ScenarioType');
