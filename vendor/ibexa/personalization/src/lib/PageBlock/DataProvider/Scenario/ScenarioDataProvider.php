<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\PageBlock\DataProvider\Scenario;

use Ibexa\Personalization\Exception\CredentialsNotFoundException;
use Ibexa\Personalization\Service\Scenario\ScenarioServiceInterface;

final class ScenarioDataProvider implements ScenarioDataProviderInterface
{
    private ScenarioServiceInterface $scenarioService;

    public function __construct(ScenarioServiceInterface $scenarioService)
    {
        $this->scenarioService = $scenarioService;
    }

    public function getScenarios(int $customerId, ?string $scenarioType = null): array
    {
        try {
            $scenarios = [];
            $scenarioList = null !== $scenarioType
                ? $this->scenarioService->getScenarioListByScenarioType($customerId, $scenarioType)
                : $this->scenarioService->getScenarioList($customerId);

            /** @var \Ibexa\Personalization\Value\Scenario\Scenario $scenario */
            foreach ($scenarioList as $scenario) {
                $scenarios[$scenario->getTitle()] = $scenario->getReferenceCode();
            }

            return $scenarios;
        } catch (CredentialsNotFoundException $exception) {
            return [];
        }
    }
}
