<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Personalization\Controller;

use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Personalization\Exception\BadResponseException;
use Ibexa\Personalization\Exception\InvalidArgumentException;
use Ibexa\Personalization\Factory\Form\PersonalizationFormFactoryInterface;
use Ibexa\Personalization\Factory\Segments\SegmentsUpdateStructFactoryInterface;
use Ibexa\Personalization\Form\Data\EditorContentData;
use Ibexa\Personalization\Form\Data\ModelData;
use Ibexa\Personalization\Form\Data\MultiCustomerAccountsData;
use Ibexa\Personalization\Form\Data\SegmentsData;
use Ibexa\Personalization\Form\Data\SubmodelData;
use Ibexa\Personalization\Form\Data\TimePeriodData;
use Ibexa\Personalization\Form\Type\ModelType;
use Ibexa\Personalization\Permission\CustomerTypeCheckerInterface;
use Ibexa\Personalization\Permission\PermissionCheckerInterface;
use Ibexa\Personalization\Service\Model\ModelServiceInterface;
use Ibexa\Personalization\Service\ModelBuild\ModelBuildServiceInterface;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use Ibexa\Personalization\Value\Model\AttributeList;
use Ibexa\Personalization\Value\Model\EditorContent;
use Ibexa\Personalization\Value\Model\EditorContentList;
use Ibexa\Personalization\Value\Model\Model;
use Ibexa\Personalization\Value\Model\ModelUpdateStruct;
use Ibexa\Personalization\Value\Model\SegmentsStruct;
use Ibexa\Personalization\Value\Model\SubmodelList;
use Ibexa\Personalization\Value\ModelBuild\State;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class ModelController extends AbstractPersonalizationController
{
    private ModelBuildServiceInterface $modelBuildService;

    private ModelServiceInterface $modelService;

    private TranslatableNotificationHandlerInterface $notificationHandler;

    private PersonalizationFormFactoryInterface $formFactory;

    private SegmentsUpdateStructFactoryInterface $segmentsUpdateStructFactory;

    public function __construct(
        PermissionCheckerInterface $permissionChecker,
        CustomerTypeCheckerInterface $customerTypeChecker,
        EventDispatcherInterface $eventDispatcher,
        SettingServiceInterface $settingService,
        ModelBuildServiceInterface $modelBuildService,
        ModelServiceInterface $modelService,
        TranslatableNotificationHandlerInterface $notificationHandler,
        PersonalizationFormFactoryInterface $formFactory,
        SegmentsUpdateStructFactoryInterface $segmentsUpdateStructFactory
    ) {
        parent::__construct($permissionChecker, $customerTypeChecker, $eventDispatcher, $settingService);

        $this->modelBuildService = $modelBuildService;
        $this->modelService = $modelService;
        $this->notificationHandler = $notificationHandler;
        $this->formFactory = $formFactory;
        $this->segmentsUpdateStructFactory = $segmentsUpdateStructFactory;
    }

    /**
     * @throws \Ibexa\Personalization\Exception\BadResponseException
     * @throws \Ibexa\Personalization\Exception\InvalidInstallationKeyException
     * @throws \Ibexa\Core\Base\Exceptions\UnauthorizedException
     */
    public function listAction(int $customerId): Response
    {
        if (($result = $this->performCredentialsCheck()) instanceof Response) {
            return $result;
        }

        $this->performAccessCheck($customerId);

        try {
            $models = $this->modelService->getModels($customerId);

            return $this->renderTemplate($customerId, '@ibexadesign/personalization/models/list.html.twig', [
                'models' => $models,
                'customer_switcher' => $this->formFactory->createMultiCustomerAccountsForm(
                    new MultiCustomerAccountsData($customerId)
                )->createView(),
                'states_colors_map' => State::STATES_COLORS_CLASSES,
            ]);
        } catch (BadResponseException $exception) {
            if (Response::HTTP_SERVICE_UNAVAILABLE === $exception->getCode()) {
                $this->notificationHandler->warning(
                    /** @Desc("Recommendation engine is not available. Please try again later.") */
                    'recommendation_engine_is_not_available',
                    [],
                    'ibexa_personalization'
                );

                return $this->renderTemplate($customerId, '@ibexadesign/personalization/models/list.html.twig', [
                    'models' => null,
                ]);
            }

            throw $exception;
        }
    }

    /**
     * @throws \Ibexa\Personalization\Exception\BadResponseException
     * @throws \Ibexa\Personalization\Exception\InvalidInstallationKeyException
     * @throws \Ibexa\Core\Base\Exceptions\UnauthorizedException
     */
    public function detailsAction(int $customerId, string $referenceCode): Response
    {
        if (($result = $this->performCredentialsCheck()) instanceof Response) {
            return $result;
        }

        $this->performAccessCheck($customerId);

        try {
            $model = $this->modelService->getModel($customerId, $referenceCode);
            $submodels = $this->modelService->getSubmodels($customerId, $referenceCode);

            return $this->renderTemplate($customerId, '@ibexadesign/personalization/models/details.html.twig', [
                'model' => $model,
                'submodels' => $submodels,
            ]);
        } catch (BadResponseException $exception) {
            if (Response::HTTP_SERVICE_UNAVAILABLE === $exception->getCode()) {
                $this->notificationHandler->warning(
                    /** @Desc("Recommendation engine is not available. Please try again later.") */
                    'recommendation_engine_is_not_available',
                    [],
                    'ibexa_personalization'
                );

                return $this->renderTemplate($customerId, '@ibexadesign/personalization/models/details.html.twig', [
                    'model' => null,
                    'submodels' => null,
                ]);
            }

            throw $exception;
        }
    }

    /**
     * @throws \Ibexa\Personalization\Exception\BadResponseException
     * @throws \Ibexa\Core\Base\Exceptions\UnauthorizedException
     */
    public function attributeAction(
        int $customerId,
        string $attributeKey,
        string $attributeType,
        ?string $attributeSource = null,
        ?string $source = null
    ): JsonResponse {
        $this->performAccessCheck($customerId);

        try {
            $attribute = $this->modelService->getAttribute(
                $customerId,
                $attributeKey,
                $attributeType,
                $attributeSource,
                $source
            );

            return new JsonResponse($attribute->getValues());
        } catch (BadResponseException $exception) {
            if (Response::HTTP_SERVICE_UNAVAILABLE === $exception->getCode()) {
                return new JsonResponse([], Response::HTTP_SERVICE_UNAVAILABLE);
            }

            throw $exception;
        }
    }

    /**
     * @throws \Ibexa\Personalization\Exception\BadResponseException
     * @throws \Ibexa\Core\Base\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     */
    public function editAction(Request $request, int $customerId, string $referenceCode): Response
    {
        if (($result = $this->performCredentialsCheck()) instanceof Response) {
            return $result;
        }

        $this->performAccessCheck($customerId);

        try {
            $model = $this->modelService->getModel($customerId, $referenceCode);
            $submodels = $this->modelService->getSubmodels($customerId, $referenceCode);
            $availableSubmodels = $this->modelService->getAttributes($customerId);
            $lastModelBuildState = $this->getLastModelBuildState($customerId, $referenceCode);
            $segments = null;

            if ($model->isSegmentsSupported()) {
                $segments = $this->modelService->getSegments(
                    $customerId,
                    $referenceCode,
                    $model->getMaximumRatingAge(),
                    $model->getValueEventType()
                );
            }

            $form = $this->getModelForm($customerId, $model, $submodels, $availableSubmodels, $segments);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                /** @var \Ibexa\Personalization\Form\Data\ModelData $data */
                $data = $form->getData();

                if ($this->isButtonClicked($form, ModelType::BUTTON_TRIGGER_MODEL_BUILD)) {
                    return $this->processTriggerModelBuild(
                        $customerId,
                        $referenceCode,
                        $data,
                        $model->isSubmodelsSupported() && count($availableSubmodels),
                    );
                }

                if ($this->isButtonClicked($form, ModelType::BUTTON_APPLY)) {
                    $submodelsEnabled = $model->isSubmodelsSupported() && count($availableSubmodels);
                    $updatedForm = $this->getFormBasedOnTimePeriod(
                        $model,
                        $data,
                        $referenceCode,
                        $customerId,
                        $submodelsEnabled
                    );
                    $model = $updatedForm->getData()->getModel();

                    return $this->renderEditTemplate($customerId, $lastModelBuildState, $model, $submodels, $updatedForm);
                }

                $modelUpdateStruct = $this->getModelUpdateStruct(
                    $model,
                    $data,
                    count($availableSubmodels)
                );

                $this->modelService->updateModel($customerId, $model, $modelUpdateStruct);

                $this->updateSegments($customerId, $model, $data, $segments);

                $button = $form->get(ModelType::BUTTON_SAVE_AND_CLOSE);
                if ($button instanceof SubmitButton && $button->isClicked()) {
                    return $this->redirectToRoute('ibexa.personalization.models', ['customerId' => $customerId]);
                }

                return $this->redirectToRoute(
                    'ibexa.personalization.model.edit',
                    [
                        'customerId' => $customerId,
                        'referenceCode' => $referenceCode,
                        'modelBuildStatus' => $lastModelBuildState,
                    ]
                );
            }

            return $this->renderEditTemplate($customerId, $lastModelBuildState, $model, $submodels, $form);
        } catch (BadResponseException $exception) {
            if (Response::HTTP_SERVICE_UNAVAILABLE === $exception->getCode()) {
                $this->notificationHandler->warning(
                    /** @Desc("Recommendation engine is not available. Please try again later.") */
                    'recommendation_engine_is_not_available',
                    [],
                    'ibexa_personalization'
                );

                return $this->renderEditTemplate($customerId);
            }

            throw $exception;
        }
    }

    /**
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     */
    private function processTriggerModelBuild(
        int $customerId,
        string $referenceCode,
        ModelData $data,
        bool $submodelsEnabled
    ): Response {
        $buildReport = $this->modelBuildService->triggerModelBuild($customerId, $referenceCode);

        if (
            null !== $buildReport
            && in_array($buildReport->getState(), State::BUILD_IN_PROGRESS_STATES, true)
        ) {
            $this->notificationHandler->success(
                /** @Desc("Model build has been triggered") */
                'model_build_has_been_triggered',
                [],
                'ibexa_personalization'
            );

            return $this->redirectToRoute('ibexa.personalization.models', ['customerId' => $customerId]);
        }

        $this->notificationHandler->error(
            /** @Desc("Failed to trigger model build") */
            'failed_to_trigger_model_build',
            [],
            'ibexa_personalization'
        );

        $model = $data->getModel();
        $submodels = new SubmodelList($data->getSubmodels() ?? []);
        $updatedForm = $this->getFormBasedOnTimePeriod(
            $model,
            $data,
            $referenceCode,
            $customerId,
            $submodelsEnabled
        );
        $state = null !== $buildReport ? State::BUILD_STATES[$buildReport->getState()] : null;

        return $this->renderEditTemplate($customerId, $state, $model, $submodels, $updatedForm);
    }

    private function renderEditTemplate(
        int $customerId,
        ?string $lastModelBuildState = null,
        ?Model $model = null,
        ?SubmodelList $submodels = null,
        ?FormInterface $form = null
    ): Response {
        $response = new Response();
        $response->setVary('X-User-Hash');
        $response->setPrivate();

        return $this->renderTemplate($customerId, '@ibexadesign/personalization/models/edit.html.twig', [
            'model' => $model,
            'submodels' => $submodels,
            'modelBuildStatus' => $lastModelBuildState,
            'form' => null !== $form ? $form->createView() : null,
            'states_colors_map' => State::STATES_COLORS_CLASSES,
        ], $response);
    }

    private function isButtonClicked(FormInterface $form, string $button): bool
    {
        if (!$form->has($button)) {
            return false;
        }

        $formButton = $form->get($button);

        return $formButton instanceof SubmitButton && $formButton->isClicked();
    }

    /**
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     */
    private function getModelUpdateStruct(
        Model $model,
        ModelData $data,
        int $availableSubmodelsCount
    ): ModelUpdateStruct {
        $isSubmodelsEnabled = $model->isSubmodelsSupported() && $availableSubmodelsCount > 0;
        $isEditorBased = $model->isEditorBased();

        $modelUpdateStruct = new ModelUpdateStruct();

        if ($model->isRelevantEventHistorySupported() || $model->isRandom()) {
            $modelUpdateStruct->setTimePeriod(
                $data->getTimePeriod()->getPeriod()
            );
        }

        if ($isSubmodelsEnabled && !empty($data->getSubmodels())) {
            $modelUpdateStruct->setSubmodels(
                new SubmodelList(
                    $data->getSubmodels()
                )
            );
        }

        if ($isEditorBased) {
            $modelUpdateStruct->setEditorContentList(
                new EditorContentList(
                    array_values(
                        array_map(
                            static function (EditorContentData $editorContentData): EditorContent {
                                return new EditorContent(
                                    $editorContentData->getId(),
                                    $editorContentData->getType()
                                );
                            },
                            $data->getEditorContentList() ?? []
                        )
                    )
                )
            );
        }

        return $modelUpdateStruct;
    }

    /**
     * @phpstan-param AttributeList<\Ibexa\Personalization\Value\Model\Attribute> $availableSubmodels
     * @phpstan-param SubmodelList<\Ibexa\Personalization\Value\Model\Submodel> $configuredSubmodels
     *
     * @return \Ibexa\Personalization\Form\Data\SubmodelData[]
     */
    private function getSubmodelsData(
        AttributeList $availableSubmodels,
        SubmodelList $configuredSubmodels
    ): array {
        $submodelsData = [];

        /** @var \Ibexa\Personalization\Value\Model\Attribute $submodel */
        foreach ($availableSubmodels as $submodel) {
            if (
                null !== $submodel->getKey()
                && null !== $submodel->getAttributeSource()
                && null !== $submodel->getType()
            ) {
                $key = $submodel->getKey();
                $submodelsData[$key] = new SubmodelData(
                    $key,
                    $submodel->getAttributeSource(),
                    $submodel->getType(),
                    $submodel->getSource(),
                    $configuredSubmodels->offsetExists($key)
                        ? $configuredSubmodels[$key]->getAttributeValues()
                        : null
                );
            }
        }

        return $submodelsData;
    }

    /**
     * @return array<\Ibexa\Personalization\Form\Data\EditorContentData>
     */
    private function getEditorContentListData(int $customerId, string $referenceCode): array
    {
        $editorContentList = $this->modelService->getEditorContentList($customerId, $referenceCode);
        $editorContentListData = [];

        /** @var \Ibexa\Personalization\Value\Model\EditorContent $editorContent */
        foreach ($editorContentList as $editorContent) {
            $editorContentListData[] = new EditorContentData(
                $editorContent->getId(),
                $editorContent->getType()
            );
        }

        return $editorContentListData;
    }

    private function getModelForm(
        int $customerId,
        Model $model,
        SubmodelList $submodels,
        AttributeList $availableSubmodels,
        ?SegmentsStruct $segments = null
    ): FormInterface {
        $configuredSubmodels = new SubmodelList();
        /** @var \Ibexa\Personalization\Value\Model\Submodel $submodel */
        foreach ($submodels as $submodel) {
            $configuredSubmodels[$submodel->getAttributeKey()] = $submodel;
        }

        $isSubmodelsEnabled = $model->isSubmodelsSupported() && count($availableSubmodels);
        $isSegmentsEnabled = $model->isSegmentsSupported() && null !== $segments && count($segments->getAllSegmentsNames()) > 0;
        $isEditorBased = $model->isEditorBased();

        $modelData = new ModelData($model->getReferenceCode(), $model);

        $timePeriodData = new TimePeriodData();
        if ($model->isRelevantEventHistorySupported() && null !== $model->getMaximumRatingAge()) {
            $timePeriodData->setPeriod($model->getMaximumRatingAge());
        }

        if ($model->isRandom() && null !== $model->getMaximumItemAge()) {
            $timePeriodData->setPeriod($model->getMaximumItemAge());
        }

        $modelData->setTimePeriod($timePeriodData);

        if ($isSubmodelsEnabled) {
            $modelData->setSubmodels(
                $this->getSubmodelsData(
                    $availableSubmodels,
                    $configuredSubmodels
                )
            );
        }

        if ($isSegmentsEnabled) {
            $modelData->setSegments(
                SegmentsData::fromSegments($segments)
            );
        }

        if ($model->isEditorBased()) {
            $modelData->setEditorContentList(
                $this->getEditorContentListData($customerId, $model->getReferenceCode())
            );
        }

        return $this->createForm(ModelType::class, $modelData, [
            'submodels_enabled' => $isSubmodelsEnabled,
            'segments_enabled' => $isSegmentsEnabled,
            'editor_based' => $isEditorBased,
            'customer_id' => $customerId,
        ]);
    }

    private function updateSegments(
        int $customerId,
        Model $model,
        ModelData $data,
        ?SegmentsStruct $segments = null
    ): void {
        if (null === $segments || !$model->isSegmentsSupported()) {
            return;
        }

        try {
            $segmentsUpdateStruct = $this->segmentsUpdateStructFactory->createFromModelData($data, $segments);
        } catch (InvalidArgumentException $e) {
            return;
        }

        $this->modelService->updateSegments(
            $customerId,
            $model,
            $segmentsUpdateStruct
        );
    }

    private function getFormBasedOnTimePeriod(
        Model $model,
        ModelData $data,
        string $referenceCode,
        int $customerId,
        bool $submodelsEnabled
    ): FormInterface {
        $modelData = new ModelData(
            $referenceCode,
            $model,
            $data->getTimePeriod(),
            $data->getSubmodels(),
            null
        );

        $segments = null;

        if ($model->isSegmentsSupported()) {
            $maximumRatingAge = $data->getTimePeriod()->getPeriod();
            $segments = $this->modelService->getSegments(
                $customerId,
                $referenceCode,
                $maximumRatingAge,
                $model->getValueEventType()
            );

            if (null !== $segments) {
                $modelData->setSegments(SegmentsData::fromSegments($segments));
                $modelData->setModel($model->withMaximumRatingAge($maximumRatingAge));
            }
        }

        $segmentsEnabled = null !== $segments;

        return $this->createForm(ModelType::class, $modelData, [
            'submodels_enabled' => $submodelsEnabled,
            'segments_enabled' => $segmentsEnabled,
            'editor_based' => $model->isEditorBased(),
            'customer_id' => $customerId,
        ]);
    }

    private function getLastModelBuildState(int $customerId, string $referenceCode): ?string
    {
        $modelBuildStatus = $this->modelBuildService->getModelBuildStatus($customerId, $referenceCode);
        if (null === $modelBuildStatus) {
            return null;
        }

        $buildReports = $modelBuildStatus->getBuildReports();
        if (null === $buildReports->getLastBuildReport()) {
            return null;
        }

        return State::BUILD_STATES[$buildReports->getLastBuildReport()->getState()];
    }
}

class_alias(ModelController::class, 'Ibexa\Platform\Bundle\Personalization\Controller\ModelController');
