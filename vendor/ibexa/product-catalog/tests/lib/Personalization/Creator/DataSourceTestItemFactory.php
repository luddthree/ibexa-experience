<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Personalization\Creator;

use Ibexa\Contracts\Personalization\Criteria\CriteriaInterface;
use Ibexa\Contracts\Personalization\Value\ItemInterface;
use Ibexa\Contracts\Personalization\Value\ItemListInterface;
use Ibexa\Contracts\Personalization\Value\ItemTypeInterface;
use Ibexa\Personalization\Criteria\Criteria;
use Ibexa\Personalization\Value\Storage\Item;
use Ibexa\Personalization\Value\Storage\ItemList;
use Ibexa\Personalization\Value\Storage\ItemType;

final class DataSourceTestItemFactory
{
    public const ITEM_BODY = 'body';
    public const ITEM_IMAGE = 'public/var/1/2/4/5/%s/%s';

    public const PRODUCT_NAME = 'Product';
    public const PRODUCT_TYPE_ID = 3;
    public const PRODUCT_TYPE_IDENTIFIER = 'product';
    public const PRODUCT_TYPE_NAME = self::PRODUCT_NAME;

    public const LANGUAGE_EN = 'eng-GB';
    public const LANGUAGE_DE = 'ger-DE';

    public const ALL_LANGUAGES = [self::LANGUAGE_EN, self::LANGUAGE_DE];

    public function createTestItem(
        int $counter,
        string $itemId,
        int $itemTypeId,
        string $itemTypeIdentifier,
        string $itemTypeName,
        string $language
    ): ItemInterface {
        return new Item(
            $itemId,
            $this->createTestItemType($itemTypeIdentifier, $itemTypeId, $itemTypeName),
            $language,
            $this->createTestItemAttributes(
                $counter,
                $itemTypeIdentifier,
                $itemTypeName,
                $language
            )
        );
    }

    public function createTestItemType(string $identifier, int $itemTypeId, string $name): ItemTypeInterface
    {
        return new ItemType(
            $identifier,
            $itemTypeId,
            $name
        );
    }

    /**
     * @return array<string, string>
     */
    public function createTestItemAttributes(
        int $counter,
        string $itemTypeIdentifier,
        string $itemTypeName,
        string $language
    ): array {
        return [
            'name' => sprintf('%s %s %s', $itemTypeName, $counter, $language),
            'body' => sprintf('%s %s %s %s', $itemTypeName, $counter, self::ITEM_BODY, $language),
            'image' => sprintf(self::ITEM_IMAGE, $itemTypeIdentifier, $counter),
        ];
    }

    /**
     * @param array<string> $identifiers
     * @param array<string> $languages
     */
    public function createTestCriteria(
        array $identifiers,
        array $languages,
        int $limit = Criteria::LIMIT,
        int $offset = 0
    ): CriteriaInterface {
        return new Criteria(
            $identifiers,
            $languages,
            $limit,
            $offset
        );
    }

    public function createTestItemList(ItemInterface ...$items): ItemListInterface
    {
        return new ItemList($items);
    }
}
