<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Factory\Form;

use Ibexa\Personalization\Form\Data\DashboardData;
use Ibexa\Personalization\Form\Data\MultiCustomerAccountsData;
use Ibexa\Personalization\Form\Data\RecommendationCallData;
use Ibexa\Personalization\Form\Data\Scenario\ScenarioData;
use Ibexa\Personalization\Form\Data\Scenario\ScenarioRemoveData;
use Ibexa\Personalization\Value\Scenario\Scenario;
use Symfony\Component\Form\FormInterface;

interface PersonalizationFormFactoryInterface
{
    public function createMultiCustomerAccountsForm(MultiCustomerAccountsData $multiCustomerAccountsData): FormInterface;

    public function createDashboardTimePeriodForm(
        int $customerId,
        DashboardData $dashboardTimePeriodData
    ): FormInterface;

    public function createScenarioForm(
        int $customerId,
        ScenarioData $scenarioData,
        string $action,
        bool $isVariantSupported
    ): FormInterface;

    public function createScenarioRemoveForm(ScenarioRemoveData $scenarioRemoveData): FormInterface;

    public function createRecommendationCallForm(
        RecommendationCallData $recommendationCallData,
        Scenario $scenario
    ): FormInterface;

    public function createDateTimeRangeForm(
        string $formName,
        string $method,
        bool $enableCustomRange = true
    ): FormInterface;
}

class_alias(PersonalizationFormFactoryInterface::class, 'Ibexa\Platform\Personalization\Factory\Form\PersonalizationFormFactoryInterface');
