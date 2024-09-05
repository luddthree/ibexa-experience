<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\Page\Storage;

use Ibexa\Contracts\Core\FieldType\StorageGateway;

/**
 * Page Field Type external storage gateway.
 */
abstract class Gateway extends StorageGateway
{
    /**
     * @param int $contentId
     * @param int $versionNo
     * @param string $languageCode
     * @param string $layout
     *
     * @return int|null
     */
    abstract public function insertPage(int $contentId, int $versionNo, string $languageCode, string $layout): ?int;

    /**
     * @param int $pageId
     */
    abstract public function removePage(int $pageId): void;

    /**
     * @param string $name
     *
     * @return int|null
     */
    abstract public function insertZone(string $name): ?int;

    /**
     * @param int $zoneId
     */
    abstract public function removeZone(int $zoneId): void;

    /**
     * @param string $type
     * @param string $name
     * @param string $view
     *
     * @return int|null
     */
    abstract public function insertBlock(string $type, string $name, string $view): ?int;

    /**
     * @param int $blockId
     * @param string|null $style
     * @param string|null $compiled
     * @param string|null $class
     *
     * @return int|null
     */
    abstract public function insertBlockDesign(int $blockId, ?string $style, ?string $compiled, ?string $class): ?int;

    /**
     * @param int $blockId
     * @param int|null $since
     * @param int|null $till
     *
     * @return int|null
     */
    abstract public function insertBlockVisibility(int $blockId, ?int $since, ?int $till): ?int;

    /**
     * @param int $blockId
     */
    abstract public function removeBlock(int $blockId): void;

    /**
     * @param int $blockId
     */
    abstract public function removeBlockDesign(int $blockId): void;

    /**
     * @param int $blockId
     */
    abstract public function removeBlockVisibility(int $blockId): void;

    /**
     * @param string $name
     * @param string|null $value
     *
     * @return int|null
     */
    abstract public function insertAttribute(string $name, ?string $value): ?int;

    /**
     * @param int $id
     */
    abstract public function removeAttribute(int $id): void;

    /**
     * @param int $attributeId
     * @param int $blockId
     */
    abstract public function assignAttributeToBlock(int $attributeId, int $blockId): void;

    /**
     * @param int $attributeId
     * @param int $blockId
     */
    abstract public function unassignAttributeFromBlock(int $attributeId, int $blockId): void;

    /**
     * @param int $blockId
     * @param int $zoneId
     */
    abstract public function assignBlockToZone(int $blockId, int $zoneId): void;

    /**
     * @param int $blockId
     * @param int $zoneId
     */
    abstract public function unassignBlockFromZone(int $blockId, int $zoneId): void;

    /**
     * @param int $zoneId
     * @param int $pageId
     */
    abstract public function assignZoneToPage(int $zoneId, int $pageId): void;

    /**
     * @param int $zoneId
     * @param int $pageId
     */
    abstract public function unassignZoneFromPage(int $zoneId, int $pageId): void;

    /**
     * @param int $pageId
     *
     * @return array
     *
     * @throws \Ibexa\FieldTypePage\Exception\PageNotFoundException When Page could not be loaded
     */
    abstract public function loadPage(int $pageId): array;

    /**
     * @param int $contentId
     * @param int|null $versionNo
     * @param string|null $languageCode
     *
     * @return array
     *
     * @throws \Ibexa\FieldTypePage\Exception\PageNotFoundException When Page could not be loaded
     */
    abstract public function loadPageByContentId(int $contentId, int $versionNo, string $languageCode): array;

    /**
     * @param int $pageId
     *
     * @return array
     */
    abstract public function loadZonesAssignedToPage(int $pageId): array;

    /**
     * @param int $pageId
     *
     * @return array
     */
    abstract public function loadBlocksAssignedToPage(int $pageId): array;

    /**
     * @param int $pageId
     *
     * @return array
     */
    abstract public function loadAttributesAssignedToPage(int $pageId): array;

    /**
     * @param int $contentId
     * @param int|null $versionNo
     * @param string[] $languageCodes
     *
     * @return array
     *
     * @throws \Ibexa\FieldTypePage\Exception\PageNotFoundException
     */
    abstract public function loadPagesMappedToContent(
        int $contentId,
        ?int $versionNo = null,
        array $languageCodes = []
    ): array;
}

class_alias(Gateway::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Storage\Gateway');
