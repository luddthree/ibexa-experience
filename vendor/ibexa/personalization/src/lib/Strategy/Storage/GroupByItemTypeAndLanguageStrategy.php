<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Strategy\Storage;

use Ibexa\Contracts\Personalization\Criteria\CriteriaInterface;
use Ibexa\Contracts\Personalization\Storage\DataSourceInterface;
use Ibexa\Contracts\Personalization\Value\ItemGroupListInterface;
use Ibexa\Contracts\Personalization\Value\ItemListInterface;
use Ibexa\Personalization\Criteria\Criteria;
use Ibexa\Personalization\Value\Storage\ItemGroup;
use Ibexa\Personalization\Value\Storage\ItemGroupList;
use Ibexa\Personalization\Value\Storage\ItemList;

final class GroupByItemTypeAndLanguageStrategy implements GroupItemStrategyInterface
{
    private const GROUP_IDENTIFIER = '%s_%s';

    public function getGroupList(DataSourceInterface $source, CriteriaInterface $criteria): ItemGroupListInterface
    {
        $groupedItems = [];
        foreach ($criteria->getItemTypeIdentifiers() as $identifier) {
            foreach ($criteria->getLanguages() as $language) {
                $countCriteria = new Criteria([$identifier], [$language]);
                $numberOfItems = $source->countItems($countCriteria);
                if ($numberOfItems === 0) {
                    continue;
                }

                $fetchItemsCriteria = new Criteria([$identifier], [$language], $numberOfItems);
                $groupIdentifier = sprintf(
                    self::GROUP_IDENTIFIER,
                    $identifier,
                    $language
                );

                $itemList = $source->fetchItems($fetchItemsCriteria);
                if (!$itemList instanceof ItemListInterface) {
                    $itemList = $this->getItemList($itemList);
                }

                $groupedItems[$groupIdentifier] = new ItemGroup($groupIdentifier, $itemList);
            }
        }

        return new ItemGroupList($groupedItems);
    }

    public static function getIndex(): string
    {
        return SupportedGroupItemStrategy::GROUP_BY_ITEM_TYPE_AND_LANGUAGE;
    }

    /**
     * @param iterable<\Ibexa\Contracts\Personalization\Value\ItemInterface> $items
     */
    private function getItemList(iterable $items): ItemListInterface
    {
        $extractedItems = [];

        foreach ($items as $item) {
            $extractedItems[$item->getId() . $item->getLanguage()] = $item;
        }

        return new ItemList($extractedItems);
    }
}

class_alias(GroupByItemTypeAndLanguageStrategy::class, 'EzSystems\EzRecommendationClient\Strategy\Storage\GroupByItemTypeAndLanguageStrategy');
