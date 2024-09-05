<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Service\Scenario;

use Ibexa\Personalization\Value\GranularityDateTimeRange;
use Ibexa\Personalization\Value\Scenario\ProfileFilterSet;
use Ibexa\Personalization\Value\Scenario\Scenario;
use Ibexa\Personalization\Value\Scenario\ScenarioList;
use Ibexa\Personalization\Value\Scenario\StandardFilterSet;

/**
 * @internal
 */
interface ScenarioServiceInterface
{
    public function getScenarioList(
        int $customerId,
        ?GranularityDateTimeRange $granularityDateTimeRange = null
    ): ScenarioList;

    public function getCalledScenarios(
        int $customerId,
        GranularityDateTimeRange $granularityDateTimeRange
    ): ScenarioList;

    public function getScenarioListByScenarioType(
        int $customerId,
        string $scenarioType
    ): ScenarioList;

    public function getScenario(
        int $customerId,
        string $scenarioName
    ): Scenario;

    public function createScenario(
        int $customerId,
        Scenario $scenario
    ): Scenario;

    public function updateScenario(
        int $customerId,
        Scenario $scenario
    ): Scenario;

    public function deleteScenario(
        int $customerId,
        string $scenarioName
    ): int;

    public function getScenarioProfileFilterSet(
        int $customerId,
        string $scenarioName
    ): ProfileFilterSet;

    public function getScenarioStandardFilterSet(
        int $customerId,
        string $scenarioName
    ): StandardFilterSet;

    public function updateScenarioProfileFilterSet(
        int $customerId,
        string $scenarioName,
        ProfileFilterSet $profileFilterSet
    ): ProfileFilterSet;

    public function updateScenarioStandardFilterSet(
        int $customerId,
        string $scenarioName,
        StandardFilterSet $standardFilterSet
    ): StandardFilterSet;
}

class_alias(ScenarioServiceInterface::class, 'Ibexa\Platform\Personalization\Service\Scenario\ScenarioServiceInterface');
