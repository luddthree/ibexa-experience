<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Factory\Form\Data;

use Ibexa\Personalization\Form\Data\Scenario\ScenarioData;
use Ibexa\Personalization\Value\Scenario\ProfileFilterSet;
use Ibexa\Personalization\Value\Scenario\Scenario;
use Ibexa\Personalization\Value\Scenario\StandardFilterSet;

interface ScenarioDataFactoryInterface
{
    public function create(
        Scenario $scenario,
        ProfileFilterSet $profileFilterSet,
        StandardFilterSet $standardFilterSet,
        bool $isCommerce
    ): ScenarioData;
}

class_alias(ScenarioDataFactoryInterface::class, 'Ibexa\Platform\Personalization\Factory\Form\Data\ScenarioDataFactoryInterface');
