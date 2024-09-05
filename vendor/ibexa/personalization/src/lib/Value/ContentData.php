<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value;

use Ibexa\Contracts\Personalization\Value\ItemListInterface;
use Ibexa\Rest\Value as RestValue;

/**
 * This class holds ContentDataVisitor structure used by Recommendation engine.
 */
final class ContentData extends RestValue
{
    private ItemListInterface $itemList;

    /** @var array<string> */
    private array $options;

    /**
     * @param array<string> $options
     */
    public function __construct(
        ItemListInterface $itemList,
        array $options = []
    ) {
        $this->itemList = $itemList;
        $this->options = $options;
    }

    public function getItemList(): ItemListInterface
    {
        return $this->itemList;
    }

    /**
     * @return string[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}

class_alias(ContentData::class, 'EzSystems\EzRecommendationClient\Value\ContentData');
