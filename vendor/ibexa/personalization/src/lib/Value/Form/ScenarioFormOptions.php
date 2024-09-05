<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Form;

use Ibexa\Personalization\Value\Content\ItemTypeList;
use Ibexa\Personalization\Value\Scenario\ProfileFilterSet;
use Ibexa\Personalization\Value\Scenario\Scenario;
use Ibexa\Personalization\Value\Scenario\StandardFilterSet;

final class ScenarioFormOptions
{
    /** @var bool */
    private $isCommerce;

    /** @var \Ibexa\Personalization\Value\Content\ItemTypeList */
    private $itemTypeList;

    /** @var \Ibexa\Personalization\Value\Scenario\Scenario|null */
    private $scenario;

    /** @var \Ibexa\Personalization\Value\Scenario\ProfileFilterSet|null */
    private $profileFilterSet;

    /** @var \Ibexa\Personalization\Value\Scenario\StandardFilterSet|null */
    private $standardFilterSet;

    public function __construct(
        bool $isCommerce,
        ItemTypeList $itemTypeList,
        ?Scenario $scenario = null,
        ?ProfileFilterSet $profileFilterSet = null,
        ?StandardFilterSet $standardFilterSet = null
    ) {
        $this->isCommerce = $isCommerce;
        $this->itemTypeList = $itemTypeList;
        $this->scenario = $scenario;
        $this->profileFilterSet = $profileFilterSet;
        $this->standardFilterSet = $standardFilterSet;
    }

    public function isCommerce(): bool
    {
        return $this->isCommerce;
    }

    public function getItemTypeList(): ItemTypeList
    {
        return $this->itemTypeList;
    }

    public function getScenario(): ?Scenario
    {
        return $this->scenario;
    }

    public function getProfileFilterSet(): ?ProfileFilterSet
    {
        return $this->profileFilterSet;
    }

    public function getStandardFilterSet(): ?StandardFilterSet
    {
        return $this->standardFilterSet;
    }
}

class_alias(ScenarioFormOptions::class, 'Ibexa\Platform\Personalization\Value\Form\ScenarioFormOptions');
