<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Data;

use Ibexa\Personalization\Value\Content\AbstractItemType;

final class RecommendationCallData
{
    private int $noOfRecommendations;

    private AbstractItemType $outputType;

    private ?string $userId;

    private ?string $categoryPathFilter;

    /** @var array<string>|null */
    private ?array $contextItems;

    /** @var array<string>|null */
    private ?array $attributes;

    /** @var array<array<string, string>>|null */
    private ?array $customParameters;

    /**
     * @param array<string>|null $contextItems
     * @param array<string>|null $attributes
     * @param array<array<string, string>>|null $customParameters
     */
    public function __construct(
        int $noOfRecommendations,
        AbstractItemType $outputType,
        ?string $userId = null,
        ?string $categoryPathFilter = null,
        ?array $contextItems = null,
        ?array $attributes = null,
        ?array $customParameters = null
    ) {
        $this->noOfRecommendations = $noOfRecommendations;
        $this->outputType = $outputType;
        $this->userId = $userId;
        $this->categoryPathFilter = $categoryPathFilter;
        $this->contextItems = $contextItems;
        $this->attributes = $attributes;
        $this->customParameters = $customParameters;
    }

    public function getNoOfRecommendations(): int
    {
        return $this->noOfRecommendations;
    }

    public function setNoOfRecommendations(int $noOfRecommendations): void
    {
        $this->noOfRecommendations = $noOfRecommendations;
    }

    public function getOutputType(): AbstractItemType
    {
        return $this->outputType;
    }

    public function setOutputType(AbstractItemType $outputType): void
    {
        $this->outputType = $outputType;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function setUserId(?string $userId): void
    {
        $this->userId = $userId;
    }

    public function getCategoryPathFilter(): ?string
    {
        return $this->categoryPathFilter;
    }

    public function setCategoryPathFilter(?string $categoryPathFilter): void
    {
        $this->categoryPathFilter = $categoryPathFilter;
    }

    /**
     * @return array<string>|null
     */
    public function getContextItems(): ?array
    {
        return $this->contextItems;
    }

    public function setContextItems(?array $contextItems): void
    {
        $this->contextItems = $contextItems;
    }

    /**
     * @return array<string>|null
     */
    public function getAttributes(): ?array
    {
        return $this->attributes;
    }

    public function setAttributes(?array $attributes): void
    {
        $this->attributes = $attributes;
    }

    public function setCustomParameters(?array $customParameters): void
    {
        $this->customParameters = $customParameters;
    }

    /**
     * @return array<string, string>|null
     */
    public function getCustomParameters(): ?array
    {
        return $this->customParameters;
    }
}

class_alias(RecommendationCallData::class, 'Ibexa\Platform\Personalization\Form\Data\RecommendationCallData');
