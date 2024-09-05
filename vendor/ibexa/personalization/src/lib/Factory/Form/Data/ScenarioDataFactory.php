<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Factory\Form\Data;

use Ibexa\Personalization\Form\Data\OptionalIntegerData;
use Ibexa\Personalization\Form\Data\OptionalTextData;
use Ibexa\Personalization\Form\Data\Scenario\ScenarioBoostItemData;
use Ibexa\Personalization\Form\Data\Scenario\ScenarioCategoryPathData;
use Ibexa\Personalization\Form\Data\Scenario\ScenarioCommerceSettingsData;
use Ibexa\Personalization\Form\Data\Scenario\ScenarioData;
use Ibexa\Personalization\Form\Data\Scenario\ScenarioExcludedCategoriesData;
use Ibexa\Personalization\Form\Data\Scenario\ScenarioExclusionsData;
use Ibexa\Personalization\Form\Data\Scenario\ScenarioRecommendMainCategoryAndSubcategoriesData;
use Ibexa\Personalization\Form\Data\Scenario\ScenarioRecommendSameCategoryPathData;
use Ibexa\Personalization\Form\Data\Scenario\ScenarioStrategyCollectionData;
use Ibexa\Personalization\Form\Data\Scenario\ScenarioStrategyData;
use Ibexa\Personalization\Form\Data\Scenario\ScenarioStrategyModelData;
use Ibexa\Personalization\Form\Data\Scenario\ScenarioStrategyModelsData;
use Ibexa\Personalization\Form\Data\Scenario\ScenarioUserProfileSettingsData;
use Ibexa\Personalization\Value\Form\ScenarioStrategyModelDataTypeOptions;
use Ibexa\Personalization\Value\Scenario\ProfileFilterSet;
use Ibexa\Personalization\Value\Scenario\Scenario;
use Ibexa\Personalization\Value\Scenario\Stage;
use Ibexa\Personalization\Value\Scenario\Stages;
use Ibexa\Personalization\Value\Scenario\StandardFilterSet;
use Ibexa\Personalization\Value\Scenario\XingModel;

final class ScenarioDataFactory implements ScenarioDataFactoryInterface
{
    public function create(
        Scenario $scenario,
        ProfileFilterSet $profileFilterSet,
        StandardFilterSet $standardFilterSet,
        bool $isCommerce
    ): ScenarioData {
        $scenarioData = new ScenarioData();

        $scenarioData
            ->setName($scenario->getTitle())
            ->setType($scenario->getType())
            ->setId($scenario->getReferenceCode())
            ->setDescription($scenario->getDescription())
            ->setInputType($scenario->getInputItemType())
            ->setOutputType($scenario->getOutputItemTypes())
            ->setStrategy($this->getScenarioStrategyCollectionData($scenario->getStages()))
            ->setUserProfileSettings(
                $this->getScenarioUserProfileSettingsData(
                    $profileFilterSet,
                    $standardFilterSet
                )
            )
            ->setExclusions($this->getExclusionsData($standardFilterSet));

        if ($isCommerce) {
            $commerceSettings = $this->getScenarioCommerceSettingsData(
                $profileFilterSet,
                $standardFilterSet
            );
            $scenarioData->setCommerceSettings($commerceSettings);
        }

        return $scenarioData;
    }

    private function getScenarioStrategyCollectionData(?Stages $stages = null): ScenarioStrategyCollectionData
    {
        $strategy = new ScenarioStrategyCollectionData();

        $strategy
            ->setPrimaryModels($this->getScenarioStrategyData(
                $stages ? $stages->getPrimaryModels() : null
            ))
            ->setFallback($this->getScenarioStrategyData(
                $stages ? $stages->getFallbackModels() : null
            ))
            ->setFailSafe($this->getScenarioStrategyData(
                $stages ? $stages->getFailSafeModels() : null
            ))
            ->setUltimatelyFailSafe($this->getScenarioStrategyData(
                $stages ? $stages->getUltimatelyFailSafeModels() : null
            ));

        return $strategy;
    }

    private function getScenarioStrategyData(?Stage $stage = null): ScenarioStrategyData
    {
        $scenarioStrategyData = new ScenarioStrategyData();

        if ($stage) {
            $scenarioStrategyData->setCategoryPath($this->getScenarioCategoryPathData($stage->getUseCategoryPath()));
            $scenarioStrategyData->setModels($this->getScenarioStrategyModelsData($stage->getXingModels()));
        } else {
            $scenarioStrategyData->setCategoryPath($this->getScenarioCategoryPathData(Stage::DEFAULT_CATEGORY_PATH));
            $scenarioStrategyData->setModels(new ScenarioStrategyModelsData());
        }

        return $scenarioStrategyData;
    }

    private function getScenarioStrategyModelsData(array $models): ScenarioStrategyModelsData
    {
        $scenarioStrategyModelsData = new ScenarioStrategyModelsData();

        if (array_key_exists(0, $models)) {
            $scenarioStrategyModelsData->setFirstModelStrategy($this->getScenarioStrategyModelData($models[0]));
        }

        if (array_key_exists(1, $models)) {
            $scenarioStrategyModelsData->setSecondModelStrategy($this->getScenarioStrategyModelData($models[1]));
        }

        return $scenarioStrategyModelsData;
    }

    private function getScenarioStrategyModelData(XingModel $model): ScenarioStrategyModelData
    {
        return new ScenarioStrategyModelData(
            $model->getModelReferenceCode(),
            $this->getScenarioStrategyModelDataType($model),
            $model->getContextFlag()
        );
    }

    private function getScenarioStrategyModelDataType(XingModel $model): string
    {
        if ($model->useSubmodels()) {
            return ScenarioStrategyModelDataTypeOptions::SUBMODELS;
        }

        if ($model->useSegments()) {
            return ScenarioStrategyModelDataTypeOptions::SEGMENTS;
        }

        return ScenarioStrategyModelDataTypeOptions::DEFAULT;
    }

    private function getScenarioCategoryPathData(?int $categoryPath = null)
    {
        $scenarioCategoryPathData = new ScenarioCategoryPathData();

        if (null === $categoryPath || $categoryPath < 0) {
            $scenarioCategoryPathData->setSameCategory($this->getScenarioRecommendSameCategoryPathData($categoryPath));
        } elseif ($categoryPath === 0) {
            $scenarioCategoryPathData->setWholeSite(true);
        } elseif ($categoryPath > 0) {
            $scenarioCategoryPathData->setMainCategoryAndSubcategories(
                $this->getScenarioRecommendMainCategoryAndSubcategoriesData($categoryPath)
            );
        }

        return $scenarioCategoryPathData;
    }

    private function getScenarioRecommendSameCategoryPathData(
        ?int $categoryPath = null
    ): ScenarioRecommendSameCategoryPathData {
        $scenarioRecommendSameCategoryPathData = new ScenarioRecommendSameCategoryPathData();
        $scenarioRecommendSameCategoryPathData->setChecked(true);
        $scenarioRecommendSameCategoryPathData->setIncludeParent(null !== $categoryPath);
        $scenarioRecommendSameCategoryPathData->setSubcategoryLevel($categoryPath);

        return $scenarioRecommendSameCategoryPathData;
    }

    private function getScenarioRecommendMainCategoryAndSubcategoriesData(
        int $categoryPath
    ): ScenarioRecommendMainCategoryAndSubcategoriesData {
        $scenarioRecommendMainCategoryAndSubcategoriesData = new ScenarioRecommendMainCategoryAndSubcategoriesData();
        $scenarioRecommendMainCategoryAndSubcategoriesData
            ->setChecked(true)
            ->setSubcategoryLevel($categoryPath);

        return $scenarioRecommendMainCategoryAndSubcategoriesData;
    }

    private function getScenarioUserProfileSettingsData(
        ProfileFilterSet $profileFilterSet,
        StandardFilterSet $standardFilterSet
    ): ScenarioUserProfileSettingsData {
        $userProfileSettings = new ScenarioUserProfileSettingsData();
        $excludeRepeatedRecommendations = new OptionalIntegerData();
        $excludeRepeatedRecommendations
            ->setEnabled(null !== $profileFilterSet->getExcludeRepeatedRecommendations())
            ->setValue($profileFilterSet->getExcludeRepeatedRecommendations());
        $userProfileSettings
            ->setExcludeContextItems($standardFilterSet->isExcludeContextItems())
            ->setExcludeAlreadyConsumed($profileFilterSet->isExcludeAlreadyConsumed())
            ->setExcludeRepeatedRecommendations($excludeRepeatedRecommendations);

        if (null !== $profileFilterSet->getAttributeBoost()) {
            $itemAttribute = $profileFilterSet->getAttributeBoost()->getItemAttributeName();
            $userAttribute = $profileFilterSet->getAttributeBoost()->getUserAttributeName();
            $boostItem = new ScenarioBoostItemData();
            $boostItem
                ->setEnabled(true)
                ->setAttribute($itemAttribute)
                ->setPosition($profileFilterSet->getAttributeBoost()->getBoost());
            $userProfileSettings->setBoostItem($boostItem);

            if ($userAttribute !== $itemAttribute) {
                $userAttributeName = new OptionalTextData();
                $userAttributeName
                    ->setEnabled(true)
                    ->setValue($profileFilterSet->getAttributeBoost()->getUserAttributeName());

                $userProfileSettings->setUserAttributeName($userAttributeName);
            }
        }

        return $userProfileSettings;
    }

    private function getExclusionsData(StandardFilterSet $standardFilterSet): ScenarioExclusionsData
    {
        return new ScenarioExclusionsData(
            $standardFilterSet->isExcludeContextItemsCategories(),
            $this->getExcludedCategoriesData($standardFilterSet)
        );
    }

    private function getExcludedCategoriesData(StandardFilterSet $standardFilterSet): ScenarioExcludedCategoriesData
    {
        if (!empty($standardFilterSet->getExcludedCategories())) {
            return new ScenarioExcludedCategoriesData(
                true,
                $standardFilterSet->getExcludedCategories()
            );
        }

        return new ScenarioExcludedCategoriesData(false);
    }

    private function getScenarioCommerceSettingsData(
        ProfileFilterSet $profileFilterSet,
        StandardFilterSet $standardFilterSet
    ): ScenarioCommerceSettingsData {
        $commerceSettings = new ScenarioCommerceSettingsData();
        $minimalItemPrice = new OptionalIntegerData();
        $minimalItemPrice->setEnabled(null !== $standardFilterSet->getMinimalItemPrice());
        $minimalItemPrice->setValue($standardFilterSet->getMinimalItemPrice());
        $commerceSettings
            ->setExcludeAlreadyPurchased($profileFilterSet->isExcludeAlreadyPurchased())
            ->setExcludeItemsWithoutPrice($standardFilterSet->isExcludeItemsWithoutPrice())
            ->setExcludeMinimalItemPrice($minimalItemPrice)
            ->setExcludeCheaperItems($standardFilterSet->getExcludeCheaperItems())
            ->setExcludeTopSellingResults($standardFilterSet->isExcludeTopSellingResults())
            ->setExcludeProductVariants($standardFilterSet->doesExcludeVariants());

        return $commerceSettings;
    }
}

class_alias(ScenarioDataFactory::class, 'Ibexa\Platform\Personalization\Factory\Form\Data\ScenarioDataFactory');
