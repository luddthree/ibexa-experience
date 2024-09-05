<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Request\Item;

use JsonSerializable;

final class ItemIdsPackage extends AbstractPackage implements JsonSerializable
{
    /** @var array<string> */
    private array $itemsIds;

    /**
     * @param array<string> $itemsIds
     */
    public function __construct(
        int $contentTypeId,
        string $contentTypeName,
        string $lang,
        array $itemsIds
    ) {
        parent::__construct($contentTypeId, $contentTypeName, $lang);

        $this->itemsIds = $itemsIds;
    }

    /**
     * @return array<string>
     */
    public function getItemsIds(): array
    {
        return $this->itemsIds;
    }

    /**
     * @return array{
     *     contentTypeId: int,
     *     contentTypeName: string,
     *     lang: string,
     *     itemIds: array<string>,
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'contentTypeId' => $this->getContentTypeId(),
            'contentTypeName' => $this->getContentTypeName(),
            'lang' => $this->getLang(),
            'itemIds' => $this->getItemsIds(),
        ];
    }
}
