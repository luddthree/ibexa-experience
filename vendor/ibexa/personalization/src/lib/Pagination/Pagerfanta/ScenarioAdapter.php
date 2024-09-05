<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Pagination\Pagerfanta;

use Ibexa\Personalization\Value\Scenario\ScenarioList;
use Pagerfanta\Adapter\AdapterInterface;

final class ScenarioAdapter implements AdapterInterface
{
    /** @var \Ibexa\Personalization\Value\Scenario\ScenarioList */
    private $scenarioList;

    public function __construct(ScenarioList $scenarioList)
    {
        $this->scenarioList = $scenarioList;
    }

    public function getNbResults(): int
    {
        return $this->scenarioList->count();
    }

    public function getSlice($offset, $length): array
    {
        return $this->scenarioList->slice($offset, $length);
    }
}

class_alias(ScenarioAdapter::class, 'Ibexa\Platform\Personalization\Pagination\Pagerfanta\ScenarioAdapter');
