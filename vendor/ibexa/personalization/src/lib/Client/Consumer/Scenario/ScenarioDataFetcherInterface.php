<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Scenario;

use Ibexa\Personalization\Value\GranularityDateTimeRange;
use Psr\Http\Message\ResponseInterface;

/**
 * @internal
 */
interface ScenarioDataFetcherInterface
{
    public function fetchScenarioList(
        int $customerId,
        string $licenseKey,
        ?GranularityDateTimeRange $granularityDateTimeRange = null
    ): ResponseInterface;

    public function fetchScenarioListByScenarioType(
        int $customerId,
        string $licenseKey,
        string $scenarioType
    ): ResponseInterface;

    public function fetchScenario(
        int $customerId,
        string $licenseKey,
        string $scenarioName
    ): ResponseInterface;

    public function fetchScenarioFilterSet(
        int $customerId,
        string $licenseKey,
        string $filterType,
        string $scenarioName
    ): ResponseInterface;
}

class_alias(ScenarioDataFetcherInterface::class, 'Ibexa\Platform\Personalization\Client\Consumer\Scenario\ScenarioDataFetcherInterface');
