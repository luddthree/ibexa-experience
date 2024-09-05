<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type;

use Ibexa\Personalization\Form\Data\ModelData;
use Ibexa\Personalization\Form\Data\TimePeriodData;
use Ibexa\Personalization\Form\Type\Model\EditorContentListType;
use Ibexa\Personalization\Form\Type\Model\SegmentsType;
use Ibexa\Personalization\Form\Type\Model\SubmodelsType;
use Ibexa\Personalization\Form\Type\Model\TimePeriodType;
use Ibexa\Personalization\Service\ModelBuild\ModelBuildServiceInterface;
use Ibexa\Personalization\Value\Model\Model;
use Ibexa\Personalization\Value\ModelBuild\State;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ModelType extends AbstractType
{
    public const BUTTON_SAVE = 'save';
    public const BUTTON_SAVE_AND_CLOSE = 'save_and_close';
    public const BUTTON_TRIGGER_MODEL_BUILD = 'trigger_model_build';
    public const BUTTON_APPLY = 'apply';

    private ModelBuildServiceInterface $modelBuildService;

    private TranslatorInterface $translator;

    private ?bool $submitButtonDisabled = null;

    public function __construct(
        ModelBuildServiceInterface $modelBuildService,
        TranslatorInterface $translator
    ) {
        $this->modelBuildService = $modelBuildService;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $customerId = $options['customer_id'];
        $segmentsEnabled = $options['segments_enabled'];

        $builder->add('referenceCode', HiddenType::class);

        if ($builder->getOption('submodels_enabled', false)) {
            $builder->add('submodels', SubmodelsType::class, [
                'submodels' => $builder->getData()->getSubmodels(),
            ]);
        }

        if ($segmentsEnabled) {
            $builder->add('segments', SegmentsType::class, [
                'segments' => $builder->getData()->getSegments(),
            ]);
        }

        if ($builder->getOption('editor_based', false)) {
            $builder->add('editorContentList', EditorContentListType::class);
        }

        $builder->addEventListener(FormEvents::PRE_SET_DATA, $this->createAddTimePeriodEventListener($segmentsEnabled));
        $builder->addEventListener(FormEvents::PRE_SET_DATA, $this->createAddSubmitButtonsEventListener($customerId));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('customer_id');
        $resolver->setAllowedTypes('customer_id', 'int');

        $resolver->setDefined([
            'submodels_enabled',
            'segments_enabled',
            'editor_based',
        ]);

        $resolver->setDefaults([
            'data_class' => ModelData::class,
            'submodels_enabled' => false,
            'segments_enabled' => false,
            'editor_based' => false,
        ]);
    }

    private function createAddTimePeriodEventListener(bool $segmentsEnabled): callable
    {
        return function (FormEvent $event) use ($segmentsEnabled): void {
            /** @var \Ibexa\Personalization\Form\Data\ModelData $data */
            $data = $event->getData();
            $model = $data->getModel();
            $form = $event->getForm();

            if ($model->isRelevantEventHistorySupported() || $model->isRandom()) {
                $this->addTimePeriodField($form, $model, $segmentsEnabled);
            }
        };
    }

    private function createAddSubmitButtonsEventListener(int $customerId): callable
    {
        return function (FormEvent $event) use ($customerId): void {
            /** @var \Ibexa\Personalization\Form\Data\ModelData $data */
            $data = $event->getData();
            $model = $data->getModel();
            $form = $event->getForm();
            $disabled = $this->isSubmitButtonDisabled($customerId, $data->getReferenceCode());

            if ($model->isRelevantEventHistorySupported()) {
                $this->addSubmitButton($form, self::BUTTON_APPLY, $disabled);
            }

            $this->addSubmitButton($form, self::BUTTON_SAVE, $disabled);
            $this->addSubmitButton($form, self::BUTTON_SAVE_AND_CLOSE, $disabled);
            $this->addSubmitButton($form, self::BUTTON_TRIGGER_MODEL_BUILD, $disabled);
        };
    }

    private function isSubmitButtonDisabled(int $customerId, string $referenceCode): bool
    {
        if (null !== $this->submitButtonDisabled) {
            return $this->submitButtonDisabled;
        }

        $modelBuildStatus = $this->modelBuildService->getModelBuildStatus($customerId, $referenceCode);

        if (
            null === $modelBuildStatus
            || null === $modelBuildStatus->getBuildReports()->getLastBuildReport()
        ) {
            $this->submitButtonDisabled = false;

            return $this->submitButtonDisabled;
        }

        $state = $modelBuildStatus->getBuildReports()->getLastBuildReport()->getState();

        $this->submitButtonDisabled = in_array($state, State::BUILD_IN_PROGRESS_STATES, true);

        return $this->submitButtonDisabled;
    }

    private function addTimePeriodField(
        FormInterface $form,
        Model $model,
        bool $segmentsEnabled
    ): void {
        $label = false;

        if ($model->isRelevantEventHistorySupported()) {
            $label = $this->translator->trans(
                /** @Desc("Relevant history") */
                'model.details.relevant_history.title',
                [],
                'ibexa_personalization'
            );
        }

        if ($model->isRandom()) {
            $label = $this->translator->trans(
                /** @Desc("Item age") */
                'model.details.item_age.title',
                [],
                'ibexa_personalization'
            );
        }

        $form->add('timePeriod', TimePeriodType::class, [
            'data_class' => TimePeriodData::class,
            'label' => $label,
            'translation_domain' => 'ibexa_personalization',
        ]);

        if ($segmentsEnabled) {
            $form->add(self::BUTTON_APPLY, SubmitType::class, [
                'attr' => [
                    'type' => 'button',
                    'hidden' => true,
                ],
            ]);
        }
    }

    private function addSubmitButton(
        FormInterface $form,
        string $button,
        bool $disabled
    ): void {
        $form->add($button, SubmitType::class, [
            'attr' => [
                'hidden' => true,
            ],
            'disabled' => $disabled,
        ]);
    }
}

class_alias(ModelType::class, 'Ibexa\Platform\Personalization\Form\Type\ModelType');
