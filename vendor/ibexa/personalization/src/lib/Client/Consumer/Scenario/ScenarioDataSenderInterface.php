<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Scenario;

use Ibexa\Personalization\Value\Scenario\Scenario;
use Psr\Http\Message\ResponseInterface;

interface ScenarioDataSenderInterface
{
    public function createScenario(
        int $customerId,
        string $licenseKey,
        Scenario $scenario
    ): ResponseInterface;

    public function updateScenario(
        int $customerId,
        string $licenseKey,
        Scenario $scenario
    ): ResponseInterface;

    public function deleteScenario(
        int $customerId,
        string $licenseKey,
        string $scenarioName
    ): ResponseInterface;

    public function updateScenarioFilterSet(
        int $customerId,
        string $licenseKey,
        string $filterType,
        string $scenarioName,
        array $body
    ): ResponseInterface;
}

class_alias(ScenarioDataSenderInterface::class, 'Ibexa\Platform\Personalization\Client\Consumer\Scenario\ScenarioDataSenderInterface');
