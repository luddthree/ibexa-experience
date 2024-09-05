<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\PageBlock\DataProvider\Scenario;

interface ScenarioDataProviderInterface
{
    /**
     * @return array<string>
     */
    public function getScenarios(int $customerId, ?string $scenarioType = null): array;
}
