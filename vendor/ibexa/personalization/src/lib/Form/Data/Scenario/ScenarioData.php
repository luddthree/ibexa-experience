<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Data\Scenario;

use Ibexa\Personalization\Validator\Constraints\CategoryPath;
use Ibexa\Personalization\Validator\Constraints\FulfilledScenarioStrategyLevel;
use Ibexa\Personalization\Value\Content\ItemType;
use Ibexa\Personalization\Value\Content\ItemTypeList;

final class ScenarioData
{
    private ?string $name;

    private ?string $type;

    private ?string $id;

    private ?ItemType $inputType;

    private ?ItemTypeList $outputType;

    private ?string $description;

    private ?ScenarioUserProfileSettingsData $userProfileSettings;

    /** @CategoryPath() */
    private ?ScenarioExclusionsData $exclusions;

    private ?ScenarioCommerceSettingsData $commerceSettings;

    /** @FulfilledScenarioStrategyLevel() */
    private ?ScenarioStrategyCollectionData $strategy;

    public function __construct(
        ?string $name = null,
        ?string $type = null,
        ?string $id = null,
        ?ItemType $inputType = null,
        ?ItemTypeList $outputType = null,
        ?string $description = null,
        ?ScenarioUserProfileSettingsData $userProfileSettings = null,
        ?ScenarioExclusionsData $exclusions = null,
        ?ScenarioCommerceSettingsData $commerceSettings = null,
        ?ScenarioStrategyCollectionData $strategy = null
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->id = $id;
        $this->inputType = $inputType;
        $this->outputType = $outputType;
        $this->description = $description;
        $this->userProfileSettings = $userProfileSettings;
        $this->exclusions = $exclusions;
        $this->commerceSettings = $commerceSettings;
        $this->strategy = $strategy;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getInputType(): ?ItemType
    {
        return $this->inputType;
    }

    public function setInputType(ItemType $inputType): self
    {
        $this->inputType = $inputType;

        return $this;
    }

    public function getOutputType(): ?ItemTypeList
    {
        return $this->outputType;
    }

    public function setOutputType(ItemTypeList $outputType): self
    {
        $this->outputType = $outputType;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description = null): self
    {
        $this->description = $description;

        return $this;
    }

    public function getUserProfileSettings(): ?ScenarioUserProfileSettingsData
    {
        return $this->userProfileSettings;
    }

    public function setUserProfileSettings(?ScenarioUserProfileSettingsData $userProfileSettings): self
    {
        $this->userProfileSettings = $userProfileSettings;

        return $this;
    }

    public function getExclusions(): ?ScenarioExclusionsData
    {
        return $this->exclusions;
    }

    public function setExclusions(?ScenarioExclusionsData $exclusions): self
    {
        $this->exclusions = $exclusions;

        return $this;
    }

    public function getCommerceSettings(): ?ScenarioCommerceSettingsData
    {
        return $this->commerceSettings;
    }

    public function setCommerceSettings(?ScenarioCommerceSettingsData $commerceSettings): self
    {
        $this->commerceSettings = $commerceSettings;

        return $this;
    }

    public function getStrategy(): ?ScenarioStrategyCollectionData
    {
        return $this->strategy;
    }

    public function setStrategy(?ScenarioStrategyCollectionData $strategy = null): self
    {
        $this->strategy = $strategy;

        return $this;
    }
}

class_alias(ScenarioData::class, 'Ibexa\Platform\Personalization\Form\Data\Scenario\ScenarioData');
