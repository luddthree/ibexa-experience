<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type\Scenario;

use Ibexa\Personalization\Form\Data\Scenario\ScenarioStrategyModelData;
use Ibexa\Personalization\Validator\Constraints\SupportedModelDataType;
use Ibexa\Personalization\Value\Form\ScenarioStrategyModelDataTypeOptions;
use Ibexa\Personalization\Value\Model\ModelList;
use JMS\TranslationBundle\Annotation\Desc;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ScenarioStrategyModelType extends AbstractType implements TranslationContainerInterface
{
    private const MODEL_DATA_TYPE_CHOICES = [
        ScenarioStrategyModelDataTypeOptions::DEFAULT => 'form.scenario.model_data_type.default',
        ScenarioStrategyModelDataTypeOptions::SUBMODELS => 'form.scenario.model_data_type.submodels',
        ScenarioStrategyModelDataTypeOptions::SEGMENTS => 'form.scenario.model_data_type.segments',
    ];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reference_code', TextType::class, [
                'required' => false,
                'label' => false,
            ])
            ->add('context', ScenarioStrategyModelContextType::class, [
                'required' => true,
                'label' => false,
            ]);

        $formModifier = $this->getFormModifier();
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            $this->onPreSetData($formModifier)
        );

        $builder->get('reference_code')->addEventListener(
            FormEvents::POST_SUBMIT,
            $this->onPostSubmit($formModifier)
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ScenarioStrategyModelData::class,
            'translation_domain' => 'ibexa_personalization',
        ]);
    }

    private function getFormModifier(): callable
    {
        return function (FormInterface $form, ?string $referenceCode) {
            $modelList = $form->getRoot()->getConfig()->getOption('model_list', new ModelList([]));

            $form->add('data_type', ChoiceType::class, [
                'label' => /** @Desc("Data type") */ 'form.scenario.model_data_type',
                'required' => true,
                'choices' => $this->getDefaultChoices(),
                'constraints' => [
                    new SupportedModelDataType(
                        [
                            'referenceCode' => $referenceCode,
                            'modelList' => $modelList,
                        ]
                    ),
                ],
            ]);
        };
    }

    private function onPreSetData(callable $formModifier): callable
    {
        return static function (FormEvent $event) use ($formModifier) {
            $referenceCode = null;
            $form = $event->getForm();
            $data = $event->getData();
            if ($data instanceof ScenarioStrategyModelData) {
                $referenceCode = $data->getReferenceCode();
            }

            $formModifier($form, $referenceCode);
        };
    }

    private function onPostSubmit(callable $formModifier): callable
    {
        return static function (FormEvent $event) use ($formModifier) {
            $form = $event->getForm();

            $formModifier($form->getParent(), $form->getData());
        };
    }

    /**
     * @return array<string>
     */
    private function getDefaultChoices(): array
    {
        return array_flip(self::MODEL_DATA_TYPE_CHOICES);
    }

    public static function getTranslationMessages(): array
    {
        return [
            Message::create('form.scenario.model_data_type.default', 'ibexa_personalization')->setDesc('Default'),
            Message::create('form.scenario.model_data_type.submodels', 'ibexa_personalization')->setDesc('Submodels'),
            Message::create('form.scenario.model_data_type.segments', 'ibexa_personalization')->setDesc('Segments'),
        ];
    }
}

class_alias(ScenarioStrategyModelType::class, 'Ibexa\Platform\Personalization\Form\Type\Scenario\ScenarioStrategyModelType');
