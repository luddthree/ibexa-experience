<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Request;

use Ibexa\Personalization\SPI\RecommendationRequest;
use Ibexa\Personalization\Value\Content\AbstractItemType;

final class BasicRecommendationRequest extends RecommendationRequest
{
    public const LIMIT_KEY = 'limit';
    public const CONTEXT_ITEMS_KEY = 'contextItems';
    public const CONTENT_TYPE_KEY = 'contentType';
    public const OUTPUT_TYPE_KEY = 'outputType';
    public const OUTPUT_TYPE_ID_KEY = 'outputTypeId';
    public const CATEGORY_PATH_KEY = 'categoryPath';
    public const LANGUAGE_KEY = 'language';
    public const ATTRIBUTES_KEY = 'attributes';
    public const FILTERS_KEY = 'filters';
    public const USE_CONTEXT_CATEGORY_PATH_KEY = 'usecontextcategorypath';
    public const RECOMMEND_CATEGORY_KEY = 'recommendCategory';
    public const SEGMENTS_KEY = 'segments';
    public const CROSS_CONTENT_TYPE_KEY = 'crossContentType';

    public int $limit;

    public string $contextItems;

    public int $contentType;

    public int $outputTypeId;

    public AbstractItemType $outputType;

    public string $language;

    /** @var array<string> */
    public array $attributes;

    /** @var array<string, string|array<string>> */
    public array $filters;

    /** @var array<int> */
    public array $segments;

    public ?string $categoryPath = null;

    public bool $usecontextcategorypath = false;

    public bool $recommendCategory = false;

    public function __construct(array $parameters)
    {
        parent::__construct($this, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestAttributes(): array
    {
        return [
            'numrecs' => $this->limit,
            'contextitems' => $this->contextItems,
            'contenttype' => $this->contentType,
            'outputtype' => $this->outputType,
            'outputtypeid' => $this->outputTypeId,
            'categorypath' => $this->categoryPath,
            'lang' => $this->language,
            'attributes' => $this->getAdditionalAttributesToQueryString($this->attributes, 'attribute'),
            'filters' => $this->extractFilters(),
            'usecontextcategorypath' => $this->usecontextcategorypath,
            'recommendCategory' => $this->recommendCategory,
            'segments' => $this->segments,
        ];
    }

    private function extractFilters(): array
    {
        $extractedFilters = [];

        foreach ($this->filters as $filterKey => $filterValue) {
            $filter = \is_array($filterValue) ? implode(',', $filterValue) : $filterValue;
            $extractedFilters[] = [$filterKey => $filter];
        }

        return $extractedFilters;
    }
}

class_alias(BasicRecommendationRequest::class, 'EzSystems\EzRecommendationClient\Request\BasicRecommendationRequest');
