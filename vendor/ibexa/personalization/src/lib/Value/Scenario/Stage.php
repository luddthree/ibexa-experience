<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Scenario;

use Ibexa\Personalization\Form\Data\Scenario\ScenarioCategoryPathData;
use Ibexa\Personalization\Form\Data\Scenario\ScenarioStrategyData;
use JsonSerializable;

final class Stage implements JsonSerializable
{
    public const DEFAULT_CATEGORY_PATH = 0;

    /** @var \Ibexa\Personalization\Value\Scenario\XingModel[]|null */
    private $xingModels;

    /** @var ?int */
    private $useCategoryPath;

    public function __construct(
        ?array $xingModels = null,
        ?int $useCategoryPath = null
    ) {
        if (null !== $xingModels) {
            foreach ($xingModels as $model) {
                if ($model instanceof XingModel) {
                    $this->xingModels[] = $model;
                }

                if (is_array($model)) {
                    $this->xingModels[] = XingModel::fromArray($model);
                }
            }
        }
        $this->useCategoryPath = $useCategoryPath;
    }

    /**
     * @return array|\Ibexa\Personalization\Value\Scenario\XingModel[]
     */
    public function getXingModels(): ?array
    {
        return $this->xingModels ?? null;
    }

    public function getUseCategoryPath(): ?int
    {
        return $this->useCategoryPath ?? null;
    }

    public static function fromArray(array $properties): self
    {
        return new self(
            $properties['xingModels'] ?? null,
            $properties['useCategoryPath'] ?? null,
        );
    }

    public static function fromScenarioStrategyData(ScenarioStrategyData $scenarioStrategyData): self
    {
        $hasSetFirstModel = null !== $scenarioStrategyData->getModels()->getFirstModelStrategy()->getReferenceCode();
        $hasSetSecondModel = null !== $scenarioStrategyData->getModels()->getSecondModelStrategy()->getReferenceCode();

        return new self(
            [
                $hasSetFirstModel ? XingModel::fromScenarioStrategyModelData(
                    $scenarioStrategyData->getModels()->getFirstModelStrategy()
                ) : null,
                $hasSetSecondModel ? XingModel::fromScenarioStrategyModelData(
                    $scenarioStrategyData->getModels()->getSecondModelStrategy()
                ) : null,
            ],
            null !== $scenarioStrategyData->getCategoryPath() ? self::decodeCategoryPath($scenarioStrategyData->getCategoryPath()) : null
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'useCategoryPath' => $this->getUseCategoryPath(),
            'xingModels' => $this->getXingModels() ?? [],
        ];
    }

    private static function decodeCategoryPath(ScenarioCategoryPathData $categoryPathData): ?int
    {
        if ($categoryPathData->getWholeSite()) {
            return self::DEFAULT_CATEGORY_PATH;
        } elseif (null !== $categoryPathData->getMainCategoryAndSubcategories() && $categoryPathData->getMainCategoryAndSubcategories()->isChecked()) {
            return $categoryPathData->getMainCategoryAndSubcategories()->getSubcategoryLevel();
        } elseif (null !== $categoryPathData->getSameCategory()) {
            if ($categoryPathData->getSameCategory()->isIncludeParent()) {
                return $categoryPathData->getSameCategory()->getSubcategoryLevel();
            }
        }

        return null;
    }
}

class_alias(Stage::class, 'Ibexa\Platform\Personalization\Value\Scenario\Stage');
