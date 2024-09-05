<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Factory\Form;

use Ibexa\Personalization\Form\Data\DashboardData;
use Ibexa\Personalization\Form\Data\DateTimeRangeData;
use Ibexa\Personalization\Form\Data\MultiCustomerAccountsData;
use Ibexa\Personalization\Form\Data\RecommendationCallData;
use Ibexa\Personalization\Form\Data\Scenario\ScenarioData;
use Ibexa\Personalization\Form\Data\Scenario\ScenarioRemoveData;
use Ibexa\Personalization\Form\Type\DashboardType;
use Ibexa\Personalization\Form\Type\DateTimeRangeType;
use Ibexa\Personalization\Form\Type\MultiCustomerAccountsType;
use Ibexa\Personalization\Form\Type\RecommendationCallType;
use Ibexa\Personalization\Form\Type\Scenario\ScenarioRemoveType;
use Ibexa\Personalization\Form\Type\Scenario\ScenarioType;
use Ibexa\Personalization\Permission\CustomerTypeCheckerInterface;
use Ibexa\Personalization\Service\Customer\CustomerInformationServiceInterface;
use Ibexa\Personalization\Service\Model\ModelServiceInterface;
use Ibexa\Personalization\Value\Scenario\Scenario;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

final class PersonalizationFormFactory implements PersonalizationFormFactoryInterface
{
    /** @var \Symfony\Component\Form\FormFactoryInterface */
    private $formFactory;

    /** @var \Ibexa\Personalization\Service\Customer\CustomerInformationServiceInterface */
    private $customerInformationService;

    /** @var \Ibexa\Personalization\Permission\CustomerTypeCheckerInterface */
    private $customerTypeChecker;

    /** @var \Ibexa\Personalization\Service\Model\ModelServiceInterface */
    private $modelService;

    /** @var \Symfony\Component\Routing\RouterInterface */
    private $router;

    public function __construct(
        FormFactoryInterface $formFactory,
        CustomerInformationServiceInterface $customerInformationService,
        CustomerTypeCheckerInterface $customerTypeChecker,
        ModelServiceInterface $modelService,
        RouterInterface $router
    ) {
        $this->formFactory = $formFactory;
        $this->customerInformationService = $customerInformationService;
        $this->customerTypeChecker = $customerTypeChecker;
        $this->modelService = $modelService;
        $this->router = $router;
    }

    public function createMultiCustomerAccountsForm(MultiCustomerAccountsData $multiCustomerAccountsData): FormInterface
    {
        return $this->formFactory->create(
            MultiCustomerAccountsType::class,
            $multiCustomerAccountsData
        );
    }

    public function createDashboardTimePeriodForm(
        int $customerId,
        DashboardData $dashboardTimePeriodData
    ): FormInterface {
        return $this->formFactory->create(
            DashboardType::class,
            $dashboardTimePeriodData,
            [
                'method' => Request::METHOD_GET,
                'is_commerce' => $this->customerTypeChecker->isCommerce($customerId),
            ]
        );
    }

    public function createScenarioForm(
        int $customerId,
        ScenarioData $scenarioData,
        string $action,
        bool $isVariantSupported
    ): FormInterface {
        return $this->formFactory->create(
            ScenarioType::class,
            $scenarioData,
            [
                'method' => Request::METHOD_POST,
                'customer_id' => $customerId,
                'item_type_list' => $this->customerInformationService->getItemTypes($customerId),
                'is_commerce' => $this->customerTypeChecker->isCommerce($customerId),
                'is_variant_supported' => $isVariantSupported,
                'action_type' => $action,
                'model_list' => $this->modelService->getModels($customerId),
            ]
        );
    }

    public function createScenarioRemoveForm(ScenarioRemoveData $scenarioRemoveData): FormInterface
    {
        return $this->formFactory->create(
            ScenarioRemoveType::class,
            $scenarioRemoveData,
            [
                'action' => $this->router->generate('ibexa.personalization.scenario.delete', [
                    'customerId' => $scenarioRemoveData->getCustomerId(),
                ]),
            ]
        );
    }

    public function createRecommendationCallForm(
        RecommendationCallData $recommendationCallData,
        Scenario $scenario
    ): FormInterface {
        return $this->formFactory->create(
            RecommendationCallType::class,
            $recommendationCallData,
            [
                'method' => Request::METHOD_POST,
                'scenario' => $scenario,
            ]
        );
    }

    public function createDateTimeRangeForm(
        string $formName,
        string $method,
        bool $enableCustomRange = true
    ): FormInterface {
        return $this->formFactory->createNamed(
            $formName,
            DateTimeRangeType::class,
            new DateTimeRangeData(),
            [
                'method' => $method,
                'custom_range' => $enableCustomRange,
            ]
        );
    }
}

class_alias(PersonalizationFormFactory::class, 'Ibexa\Platform\Personalization\Factory\Form\PersonalizationFormFactory');
