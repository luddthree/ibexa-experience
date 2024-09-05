<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Recommendation;

use Ibexa\Personalization\Form\Data\RecommendationCallData;
use Ibexa\Personalization\Value\Content\AbstractItemType;
use Ibexa\Personalization\Value\Content\CrossContentType;
use Ibexa\Personalization\Value\Content\ItemType;

final class Request
{
    public const LIMIT_KEY = 'numrecs';
    public const CATEGORY_PATH_KEY = 'categorypath';
    public const CONTEXT_ITEMS_KEY = 'contextitems';
    public const ATTRIBUTE_KEY = 'attribute';
    public const CROSS_CONTENT_TYPE_KEY = 'crosscontenttype';
    public const OUTPUT_TYPE_ID_KEY = 'outputtypeid';

    private int $noOfRecommendations;

    private AbstractItemType $outputType;

    private string $userId;

    private ?string $categoryPathFilter;

    /** @var array<string>|null */
    private ?array $contextItems;

    /** @var array<string>|null */
    private ?array $attributes;

    /** @var array<string, string>|null */
    private ?array $customParameters;

    private function __construct(
        int $noOfRecommendations,
        AbstractItemType $outputType,
        string $userId,
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

    public function getOutputType(): AbstractItemType
    {
        return $this->outputType;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getCategoryPathFilter(): ?string
    {
        return $this->categoryPathFilter;
    }

    /**
     * @return array<string>|null
     */
    public function getContextItems(): ?array
    {
        return $this->contextItems;
    }

    /**
     * @return array<string>|null
     */
    public function getAttributes(): ?array
    {
        return $this->attributes;
    }

    /**
     * @return array<string, string>|null
     */
    public function getCustomParameters(): ?array
    {
        return $this->customParameters;
    }

    /**
     * @phpstan-param array{
     *  'limit': int,
     *  'outputType': AbstractItemType,
     *  'userId': string,
     *  'categoryPathFilter': ?string,
     *  'contextItems'?: array<string>|null,
     *  'attributes': ?array<string>,
     *  'customParameters'?: ?array<string, string>,
     * } $properties
     */
    public static function fromArray(array $properties): self
    {
        return new self(
            (int) $properties['limit'],
            $properties['outputType'],
            $properties['userId'],
            $properties['categoryPathFilter'] ?? null,
            $properties['contextItems'] ?? null,
            $properties['attributes'] ?? null,
            $properties['customParameters'] ?? null,
        );
    }

    public static function fromRecommendationCallData(RecommendationCallData $recommendationCallData): self
    {
        return new self(
            $recommendationCallData->getNoOfRecommendations(),
            $recommendationCallData->getOutputType(),
            $recommendationCallData->getUserId(),
            $recommendationCallData->getCategoryPathFilter(),
            $recommendationCallData->getContextItems(),
            $recommendationCallData->getAttributes(),
            $recommendationCallData->getCustomParameters()
                ? self::parseCustomParameters($recommendationCallData->getCustomParameters())
                : null
        );
    }

    public function getQueryStringParameters(): array
    {
        $attribute = !empty($this->getAttributes())
            ? implode(',', $this->getAttributes())
            : null;

        $contextItems = !empty($this->getContextItems())
            ? implode(',', $this->getContextItems())
            : null;

        $queryStringParameters = [
            self::LIMIT_KEY => $this->getNoOfRecommendations(),
            self::CATEGORY_PATH_KEY => $this->getCategoryPathFilter(),
            self::CONTEXT_ITEMS_KEY => $contextItems,
            self::ATTRIBUTE_KEY => $attribute,
        ];

        $outputType = $this->getOutputType();
        if ($outputType instanceof CrossContentType) {
            $queryStringParameters[self::CROSS_CONTENT_TYPE_KEY] = true;
        } elseif ($outputType instanceof ItemType) {
            $queryStringParameters[self::OUTPUT_TYPE_ID_KEY] = $outputType->getId();
        }

        if (null !== $this->getCustomParameters()) {
            $queryStringParameters = array_merge(
                $queryStringParameters,
                $this->getCustomParameters()
            );
        }

        return $queryStringParameters;
    }

    private static function parseCustomParameters(array $customParameters): array
    {
        $parameters = [];

        foreach ($customParameters as $customParameter) {
            foreach ($customParameter as $name => $value) {
                $parameters[$name] = $value;
            }
        }

        return $parameters;
    }
}

class_alias(Request::class, 'Ibexa\Platform\Personalization\Value\Recommendation\Request');
