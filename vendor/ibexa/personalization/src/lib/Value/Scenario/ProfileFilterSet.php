<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Scenario;

use Ibexa\Personalization\Form\Data\OptionalIntegerData;
use Ibexa\Personalization\Form\Data\OptionalTextData;
use Ibexa\Personalization\Form\Data\Scenario\ScenarioBoostItemData;
use Ibexa\Personalization\Form\Data\Scenario\ScenarioCommerceSettingsData;
use Ibexa\Personalization\Form\Data\Scenario\ScenarioUserProfileSettingsData;
use JsonSerializable;

final class ProfileFilterSet implements JsonSerializable
{
    private bool $excludeAlreadyPurchased;

    private bool $excludeAlreadyConsumed;

    private ?int $excludeRepeatedRecommendations;

    private ?AttributeBoost $attributeBoost;

    public function __construct(
        bool $excludeAlreadyPurchased,
        bool $excludeAlreadyConsumed,
        ?int $excludeRepeatedRecommendations = null,
        ?AttributeBoost $attributeBoost = null
    ) {
        $this->excludeAlreadyPurchased = $excludeAlreadyPurchased;
        $this->excludeAlreadyConsumed = $excludeAlreadyConsumed;
        $this->excludeRepeatedRecommendations = $excludeRepeatedRecommendations;
        $this->attributeBoost = $attributeBoost;
    }

    public function isExcludeAlreadyPurchased(): bool
    {
        return $this->excludeAlreadyPurchased;
    }

    public function isExcludeAlreadyConsumed(): bool
    {
        return $this->excludeAlreadyConsumed;
    }

    public function getExcludeRepeatedRecommendations(): ?int
    {
        return $this->excludeRepeatedRecommendations;
    }

    public function getAttributeBoost(): ?AttributeBoost
    {
        return $this->attributeBoost;
    }

    public static function fromArray(array $properties): self
    {
        return new self(
            $properties['excludeAlreadyPurchased'] ?? false,
            $properties['excludeAlreadyConsumed'] ?? false,
            $properties['excludeRepeatedRecommendations'] ?? null,
            isset($properties['attributeBoost'])
                ? AttributeBoost::fromArray($properties['attributeBoost'])
                : null,
        );
    }

    public static function fromScenarioSettingsData(
        ?ScenarioUserProfileSettingsData $profileSettingsData = null,
        ?ScenarioCommerceSettingsData $commerceSettingsData = null
    ): self {
        $excludeAlreadyPurchased = false;
        $boost = null;
        $excludeAlreadyConsumed = false;
        $excludeRepeatedRecommendations = null;

        if (null !== $profileSettingsData) {
            $boost = self::extractAttributeBoost(
                $profileSettingsData->getBoostItem(),
                $profileSettingsData->getUserAttributeName()
            );
            $excludeAlreadyConsumed = $profileSettingsData->isExcludeAlreadyConsumed() ?? false;
            $excludeRepeatedRecommendations = self::extractExcludeRepeatedRecommendations(
                $profileSettingsData->getExcludeRepeatedRecommendations()
            );
        }

        if (null !== $commerceSettingsData) {
            $excludeAlreadyPurchased = $commerceSettingsData->isExcludeAlreadyPurchased() ?? false;
        }

        return new ProfileFilterSet(
            $excludeAlreadyPurchased,
            $excludeAlreadyConsumed,
            $excludeRepeatedRecommendations,
            $boost
        );
    }

    private static function extractAttributeBoost(
        ?ScenarioBoostItemData $boostItemData = null,
        ?OptionalTextData $textData = null
    ): ?AttributeBoost {
        if (
            null !== $boostItemData
            && $boostItemData->isEnabled()
            && null !== $boostItemData->getAttribute()
        ) {
            return AttributeBoost::fromArray([
                'itemAttributeName' => $boostItemData->getAttribute(),
                'userAttributeName' => self::extractUserAttributeName($textData) ?? $boostItemData->getAttribute(),
                'boost' => $boostItemData->getPosition(),
            ]);
        }

        return null;
    }

    private static function extractUserAttributeName(?OptionalTextData $textData = null): ?string
    {
        if (
            null !== $textData
            && $textData->isEnabled()
            && null !== $textData->getValue()
        ) {
            return $textData->getValue();
        }

        return null;
    }

    private static function extractExcludeRepeatedRecommendations(?OptionalIntegerData $optionalIntegerData = null): ?int
    {
        if (null !== $optionalIntegerData && $optionalIntegerData->isEnabled()) {
            return $optionalIntegerData->getValue();
        }

        return null;
    }

    public function jsonSerialize(): array
    {
        return [
            'excludeAlreadyPurchased' => $this->isExcludeAlreadyPurchased(),
            'excludeAlreadyConsumed' => $this->isExcludeAlreadyConsumed(),
            'excludeRepeatedRecommendations' => $this->getExcludeRepeatedRecommendations(),
            'attributeBoost' => $this->getAttributeBoost(),
        ];
    }
}

class_alias(ProfileFilterSet::class, 'Ibexa\Platform\Personalization\Value\Scenario\ProfileFilterSet');
