<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Personalization\Controller;

use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Personalization\Exception\BadResponseException;
use Ibexa\Personalization\Factory\DateTime\GranularityFactoryInterface;
use Ibexa\Personalization\Factory\Form\Data\ScenarioDataFactoryInterface;
use Ibexa\Personalization\Factory\Form\PersonalizationFormFactoryInterface;
use Ibexa\Personalization\Form\Data\MultiCustomerAccountsData;
use Ibexa\Personalization\Form\Data\RecommendationCallData;
use Ibexa\Personalization\Form\Data\Scenario\ScenarioCommerceSettingsData;
use Ibexa\Personalization\Form\Data\Scenario\ScenarioData;
use Ibexa\Personalization\Form\Data\Scenario\ScenarioExclusionsData;
use Ibexa\Personalization\Form\Data\Scenario\ScenarioRemoveData;
use Ibexa\Personalization\Form\Data\Scenario\ScenarioUserProfileSettingsData;
use Ibexa\Personalization\Form\PersonalizationForm;
use Ibexa\Personalization\Form\Type\Scenario\ScenarioType;
use Ibexa\Personalization\Mapper\OutputTypeAttributesMapperInterface;
use Ibexa\Personalization\Pagination\Pagerfanta\ScenarioAdapter;
use Ibexa\Personalization\Permission\CustomerTypeCheckerInterface;
use Ibexa\Personalization\Permission\PermissionCheckerInterface;
use Ibexa\Personalization\Service\Customer\CustomerInformationServiceInterface;
use Ibexa\Personalization\Service\Model\ModelServiceInterface;
use Ibexa\Personalization\Service\Recommendation\RecommendationServiceInterface;
use Ibexa\Personalization\Service\Scenario\ScenarioServiceInterface;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use Ibexa\Personalization\Value\Content\CrossContentType;
use Ibexa\Personalization\Value\Content\ItemType;
use Ibexa\Personalization\Value\Recommendation\Request as RecommendationRequest;
use Ibexa\Personalization\Value\Scenario\ProfileFilterSet;
use Ibexa\Personalization\Value\Scenario\Scenario;
use Ibexa\Personalization\Value\Scenario\StandardFilterSet;
use Ibexa\Personalization\Value\TimePeriod;
use JMS\TranslationBundle\Annotation\Desc;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Form\Button;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class ScenarioController extends AbstractPersonalizationController
{
    private const DEFAULT_PAGE = '1';

    private ScenarioServiceInterface $scenarioService;

    private RecommendationServiceInterface $recommendationService;

    private ModelServiceInterface $modelService;

    private GranularityFactoryInterface $granularityFactory;

    private PersonalizationFormFactoryInterface $formFactory;

    private ScenarioDataFactoryInterface $scenarioDataFactory;

    private TranslatableNotificationHandlerInterface $notificationHandler;

    private OutputTypeAttributesMapperInterface $outputTypeAttributesMapper;

    private CustomerInformationServiceInterface $customerInformationService;

    public function __construct(
        PermissionCheckerInterface $permissionChecker,
        CustomerTypeCheckerInterface $customerTypeChecker,
        EventDispatcherInterface $eventDispatcher,
        SettingServiceInterface $settingService,
        ScenarioServiceInterface $scenarioService,
        RecommendationServiceInterface $recommendationService,
        ModelServiceInterface $modelService,
        GranularityFactoryInterface $granularityFactory,
        PersonalizationFormFactoryInterface $formFactory,
        ScenarioDataFactoryInterface $scenarioDataFactory,
        TranslatableNotificationHandlerInterface $notificationHandler,
        OutputTypeAttributesMapperInterface $outputTypeAttributesMapper,
        CustomerInformationServiceInterface $customerInformationService
    ) {
        parent::__construct($permissionChecker, $customerTypeChecker, $eventDispatcher, $settingService);

        $this->scenarioService = $scenarioService;
        $this->recommendationService = $recommendationService;
        $this->modelService = $modelService;
        $this->granularityFactory = $granularityFactory;
        $this->formFactory = $formFactory;
        $this->scenarioDataFactory = $scenarioDataFactory;
        $this->notificationHandler = $notificationHandler;
        $this->outputTypeAttributesMapper = $outputTypeAttributesMapper;
        $this->customerInformationService = $customerInformationService;
    }

    /**
     * @throws \Ibexa\Personalization\Exception\BadResponseException
     * @throws \Ibexa\Personalization\Exception\InvalidInstallationKeyException
     * @throws \Ibexa\Core\Base\Exceptions\UnauthorizedException
     */
    public function scenariosAction(Request $request, int $customerId): Response
    {
        if (($result = $this->performCredentialsCheck()) instanceof Response) {
            return $result;
        }

        $this->performAccessCheck($customerId);

        $page = (int)$request->query->get('page', self::DEFAULT_PAGE);
        $form = $this->formFactory->createDateTimeRangeForm(
            PersonalizationForm::SCENARIO_FORM,
            Request::METHOD_GET,
            false
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $granularityDateTimeRange = $form->getData()->getPeriod();
        } else {
            $granularityDateTimeRange = $this->granularityFactory->createFromInterval(TimePeriod::LAST_24_HOURS);
        }

        try {
            $pagerfanta = new Pagerfanta(
                new ScenarioAdapter(
                    $this->scenarioService->getScenarioList(
                        $customerId,
                        $granularityDateTimeRange
                    )
                )
            );

            $maxPerPage = $this->getConfigResolver()->getParameter('personalization.pagination.limit');
            $pagerfanta->setMaxPerPage($maxPerPage);
            $pagerfanta->setCurrentPage((int)min($page, $pagerfanta->getNbPages()));

            return $this->renderTemplate(
                $customerId,
                '@ibexadesign/personalization/scenarios/list.html.twig',
                [
                    'pager' => $pagerfanta,
                    'scenario_form' => $form->createView(),
                    'customer_switcher' => $this->formFactory->createMultiCustomerAccountsForm(
                        new MultiCustomerAccountsData($customerId)
                    )->createView(),
                ]
            );
        } catch (BadResponseException $exception) {
            if (Response::HTTP_SERVICE_UNAVAILABLE === $exception->getCode()) {
                $this->notificationHandler->warning(
                    /** @Desc("Recommendation engine is not available. Please try again later.") */
                    'recommendation_engine_is_not_available',
                    [],
                    'ibexa_personalization'
                );

                return $this->renderTemplate(
                    $customerId,
                    '@ibexadesign/personalization/scenarios/list.html.twig',
                    [
                        'pager' => null,
                        'scenario_form' => $form->createView(),
                    ]
                );
            }

            throw $exception;
        }
    }

    /**
     * @throws \Ibexa\Personalization\Exception\BadResponseException
     * @throws \Ibexa\Personalization\Exception\InvalidInstallationKeyException
     * @throws \Ibexa\Core\Base\Exceptions\UnauthorizedException
     */
    public function createAction(Request $request, int $customerId): Response
    {
        if (($result = $this->performCredentialsCheck()) instanceof Response) {
            return $result;
        }

        $this->performAccessCheck($customerId);

        $features = $this->customerInformationService->getFeatures($customerId);

        /** @var \Symfony\Component\Form\Form $scenarioForm */
        $scenarioForm = $this->formFactory->createScenarioForm(
            $customerId,
            new ScenarioData(),
            ScenarioType::CREATE_ACTION,
            $features->isVariantSupported()
        );
        $scenarioForm->handleRequest($request);

        if ($scenarioForm->isSubmitted() && $scenarioForm->isValid()) {
            try {
                /** @var \Ibexa\Personalization\Form\Data\Scenario\ScenarioData $scenarioData */
                $scenarioData = $scenarioForm->getData();
                $scenario = $this->scenarioService->createScenario(
                    $customerId,
                    Scenario::fromScenarioData($scenarioData)
                );
                $this->updateScenarioFilterSets(
                    $customerId,
                    $scenario->getReferenceCode(),
                    $scenarioData->getUserProfileSettings(),
                    $scenarioData->getCommerceSettings(),
                    $scenarioData->getExclusions()
                );

                if ($scenarioForm->getClickedButton() instanceof Button
                    && $scenarioForm->getClickedButton()->getName() === ScenarioType::BTN_SAVE
                ) {
                    return $this->redirectToRoute('ibexa.personalization.scenario.edit', [
                        'name' => $scenario->getReferenceCode(),
                        'customerId' => $customerId,
                    ]);
                }

                return $this->redirectToRoute('ibexa.personalization.scenarios', [
                    'customerId' => $customerId,
                ]);
            } catch (BadResponseException $exception) {
                if (Response::HTTP_SERVICE_UNAVAILABLE === $exception->getCode()) {
                    $this->notificationHandler->warning(
                        /** @Desc("Recommendation engine is not available. Please try again later.") */
                        'recommendation_engine_is_not_available',
                        [],
                        'ibexa_personalization'
                    );

                    return $this->render('@ibexadesign/personalization/scenarios/create.html.twig', [
                        'scenario_form' => $scenarioForm->createView(),
                        'models' => null,
                        'is_commerce' => $this->customerTypeChecker->isCommerce($customerId),
                        'customer_id' => $customerId,
                    ]);
                }

                throw $exception;
            }
        }

        return $this->render('@ibexadesign/personalization/scenarios/create.html.twig', [
            'scenario_form' => $scenarioForm->createView(),
            'models' => $this->modelService->getModels($customerId),
            'is_commerce' => $this->customerTypeChecker->isCommerce($customerId),
            'customer_id' => $customerId,
        ]);
    }

    /**
     * @throws \Ibexa\Personalization\Exception\BadResponseException
     * @throws \Ibexa\Personalization\Exception\InvalidInstallationKeyException
     * @throws \Ibexa\Core\Base\Exceptions\UnauthorizedException
     */
    public function detailsAction(int $customerId, string $name): Response
    {
        if (($result = $this->performCredentialsCheck()) instanceof Response) {
            return $result;
        }

        $this->performAccessCheck($customerId);

        try {
            $scenario = $this->scenarioService->getScenario(
                $customerId,
                $name
            );

            return $this->renderTemplate(
                $customerId,
                '@ibexadesign/personalization/scenarios/details.html.twig',
                [
                    'scenario' => $scenario,
                    'standardFilterSet' => $this->scenarioService->getScenarioStandardFilterSet($customerId, $name),
                    'profileFilterSet' => $this->scenarioService->getScenarioProfileFilterSet($customerId, $name),
                    'is_commerce' => $this->customerTypeChecker->isCommerce($customerId),
                ]
            );
        } catch (BadResponseException $exception) {
            if (Response::HTTP_SERVICE_UNAVAILABLE === $exception->getCode()) {
                $this->notificationHandler->warning(
                    /** @Desc("Recommendation engine is not available. Please try again later.") */
                    'recommendation_engine_is_not_available',
                    [],
                    'ibexa_personalization'
                );

                return $this->renderTemplate(
                    $customerId,
                    '@ibexadesign/personalization/scenarios/details.html.twig',
                    [
                        'scenario' => null,
                        'standardFilterSet' => null,
                        'profileFilterSet' => null,
                        'is_commerce' => $this->customerTypeChecker->isCommerce($customerId),
                    ]
                );
            }

            throw $exception;
        }
    }

    /**
     * @throws \Ibexa\Personalization\Exception\InvalidInstallationKeyException
     * @throws \Ibexa\Core\Base\Exceptions\UnauthorizedException
     */
    public function previewAction(int $customerId, string $name): Response
    {
        if (($result = $this->performCredentialsCheck()) instanceof Response) {
            return $result;
        }

        $this->performAccessCheck($customerId);

        $scenario = $this->scenarioService->getScenario($customerId, $name);
        $outputType = $scenario->getOutputItemTypes()->getFirst();
        $recommendationCallData = new RecommendationCallData(
            $this->getConfigResolver()->getParameter('personalization.recommendations.limit'),
            $outputType
        );

        if ($outputType instanceof CrossContentType) {
            $recommendationCallData->setAttributes(
                $this->outputTypeAttributesMapper->getAttributesByScenario($customerId, $scenario)
            );
        }

        if ($outputType instanceof ItemType) {
            $recommendationCallData->setAttributes(
                $this->outputTypeAttributesMapper->getAttributesByOutputTypeId($customerId, $outputType->getId())
            );
        }

        $form = $this->formFactory->createRecommendationCallForm($recommendationCallData, $scenario);
        $preview = $this->recommendationService->getRecommendationsPreview(
            $customerId,
            $name,
            $this->getRecommendationRequest($form->getData())
        );

        return $this->renderTemplate(
            $customerId,
            '@ibexadesign/personalization/scenarios/preview.html.twig',
            [
                'scenario' => $scenario,
                'recommendation_call_form' => $form->createView(),
                'preview' => $preview,
                'can_edit' => $this->permissionChecker->canEdit($customerId),
            ]
        );
    }

    /**
     * @throws \Ibexa\Personalization\Exception\BadResponseException
     * @throws \Ibexa\Personalization\Exception\InvalidInstallationKeyException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function editAction(Request $request, int $customerId, string $name): Response
    {
        if (($result = $this->performCredentialsCheck()) instanceof Response) {
            return $result;
        }

        $this->performAccessCheck($customerId);

        try {
            $scenario = $this->scenarioService->getScenario($customerId, $name);
            $scenarioData = $this->getScenarioData($customerId, $scenario);
            $features = $this->customerInformationService->getFeatures($customerId);
            $isVariantSupported = $features->isVariantSupported();
            $scenarioForm = $this->formFactory->createScenarioForm(
                $customerId,
                $scenarioData,
                ScenarioType::EDIT_ACTION,
                $isVariantSupported
            );
            $scenarioForm->handleRequest($request);
            $scenarioRemoveData = new ScenarioRemoveData($customerId, $scenario->getReferenceCode());
            $scenarioRemoveForm = $this->formFactory->createScenarioRemoveForm($scenarioRemoveData);
            if ($scenarioForm->isSubmitted() && $scenarioForm->isValid()) {
                $scenarioData = $scenarioForm->getData();
                $scenario = $this->updateScenario($customerId, $scenarioData);

                if (
                    $scenarioForm instanceof Form
                    && $scenarioForm->getClickedButton() instanceof Button
                    && $scenarioForm->getClickedButton()->getName() === ScenarioType::BTN_SAVE_AND_CLOSE
                ) {
                    return $this->redirectToRoute(
                        'ibexa.personalization.scenarios',
                        [
                            'customerId' => $customerId,
                        ]
                    );
                }

                $scenarioForm = $this->formFactory->createScenarioForm(
                    $customerId,
                    $scenarioData,
                    ScenarioType::EDIT_ACTION,
                    $isVariantSupported
                );
            }

            return $this->render('@ibexadesign/personalization/scenarios/edit.html.twig', [
                'scenario' => $scenario,
                'scenario_form' => $scenarioForm->createView(),
                'scenario_remove_form' => $scenarioRemoveForm->createView(),
                'models' => $this->modelService->getModels($customerId),
                'is_commerce' => $this->customerTypeChecker->isCommerce($customerId),
                'customer_id' => $customerId,
            ]);
        } catch (BadResponseException $exception) {
            if (Response::HTTP_SERVICE_UNAVAILABLE === $exception->getCode()) {
                $this->notificationHandler->warning(
                    /** @Desc("Recommendation engine is not available. Please try again later.") */
                    'recommendation_engine_is_not_available',
                    [],
                    'ibexa_personalization'
                );

                return $this->render('@ibexadesign/personalization/scenarios/edit.html.twig', [
                    'scenario' => null,
                    'scenario_form' => null,
                    'scenario_remove_form' => null,
                    'models' => null,
                    'is_commerce' => $this->customerTypeChecker->isCommerce($customerId),
                    'customer_id' => $customerId,
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
    public function deleteAction(Request $request, int $customerId): Response
    {
        if (($result = $this->performCredentialsCheck()) instanceof Response) {
            return $result;
        }

        $this->performAccessCheck($customerId);

        $form = $this->formFactory->createScenarioRemoveForm(new ScenarioRemoveData($customerId));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $referenceCode = $data->getReferenceCode();

            try {
                if (Response::HTTP_ACCEPTED !== $this->scenarioService->deleteScenario($customerId, $referenceCode)) {
                    return new Response(
                        'Could not remove scenario: ' . $referenceCode,
                        Response::HTTP_BAD_REQUEST
                    );
                }

                return $this->redirectToRoute('ibexa.personalization.scenarios', [
                    'customerId' => $customerId,
                ]);
            } catch (BadResponseException $exception) {
                if (Response::HTTP_SERVICE_UNAVAILABLE === $exception->getCode()) {
                    $this->notificationHandler->warning(
                        /** @Desc("Recommendation engine is not available. Please try again later.") */
                        'recommendation_engine_is_not_available',
                        [],
                        'ibexa_personalization'
                    );

                    return $this->redirectToRoute('ibexa.personalization.scenarios', [
                        'customerId' => $customerId,
                    ]);
                }

                throw $exception;
            }
        }

        return new Response('Invalid request', Response::HTTP_FORBIDDEN);
    }

    private function updateScenario(int $customerId, ScenarioData $scenarioData): Scenario
    {
        $scenario = $this->scenarioService->updateScenario(
            $customerId,
            Scenario::fromScenarioData($scenarioData)
        );

        $this->updateScenarioFilterSets(
            $customerId,
            $scenario->getReferenceCode(),
            $scenarioData->getUserProfileSettings(),
            $scenarioData->getCommerceSettings(),
            $scenarioData->getExclusions()
        );

        return $scenario;
    }

    private function updateScenarioFilterSets(
        int $customerId,
        string $scenarioName,
        ?ScenarioUserProfileSettingsData $profileSettingsData = null,
        ?ScenarioCommerceSettingsData $commerceSettingsData = null,
        ?ScenarioExclusionsData $exclusionsData = null
    ): void {
        $this->scenarioService->updateScenarioProfileFilterSet(
            $customerId,
            $scenarioName,
            ProfileFilterSet::fromScenarioSettingsData(
                $profileSettingsData,
                $commerceSettingsData
            )
        );

        $this->scenarioService->updateScenarioStandardFilterSet(
            $customerId,
            $scenarioName,
            StandardFilterSet::fromScenarioSettingsData(
                $profileSettingsData,
                $commerceSettingsData,
                $exclusionsData
            )
        );
    }

    private function getScenarioData(int $customerId, Scenario $scenario): ScenarioData
    {
        return $this->scenarioDataFactory->create(
            $scenario,
            $this->scenarioService->getScenarioProfileFilterSet($customerId, $scenario->getReferenceCode()),
            $this->scenarioService->getScenarioStandardFilterSet($customerId, $scenario->getReferenceCode()),
            $this->customerTypeChecker->isCommerce($customerId)
        );
    }

    private function getRecommendationRequest(
        RecommendationCallData $recommendationCallData
    ): RecommendationRequest {
        $userId = $recommendationCallData->getUserId()
            ?? $this->getConfigResolver()
                ->getParameter('personalization.recommendations.user_id');

        $recommendationCallData->setUserId($userId);

        return RecommendationRequest::fromRecommendationCallData($recommendationCallData);
    }
}

class_alias(ScenarioController::class, 'Ibexa\Platform\Bundle\Personalization\Controller\ScenarioController');
