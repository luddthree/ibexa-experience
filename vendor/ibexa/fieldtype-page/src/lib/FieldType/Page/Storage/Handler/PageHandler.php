<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\Page\Storage\Handler;

use Ibexa\FieldTypePage\Exception\PageNotFoundException;
use Ibexa\FieldTypePage\FieldType\Page\Storage\Gateway;

class PageHandler implements PageHandlerInterface
{
    /** @var \Ibexa\FieldTypePage\FieldType\Page\Storage\Gateway */
    private $gateway;

    public function __construct(Gateway $gateway)
    {
        $this->gateway = $gateway;
    }

    public function loadPageByContentId(int $contentId, int $versionNo, string $languageCode): array
    {
        $page = $this->gateway->loadPageByContentId($contentId, $versionNo, $languageCode);

        $page = [
            'id' => (int) $page['id'],
            'layout' => $page['layout'],
            'content_id' => $page['content_id'],
            'version_no' => $page['version_no'],
            'language_code' => $page['language_code'],
            'zones' => [],
        ];

        foreach ($this->gateway->loadZonesAssignedToPage($page['id']) as $zone) {
            $page['zones'][$zone['id']] = [
                'id' => $zone['id'],
                'name' => $zone['name'],
                'blocks' => [],
            ];
        }

        foreach ($this->gateway->loadBlocksAssignedToPage($page['id']) as $block) {
            $page['zones'][$block['zone_id']]['blocks'][$block['id']] = [
                'id' => $block['id'],
                'type' => $block['type'],
                'view' => $block['view'],
                'name' => $block['name'],
                'class' => $block['class'],
                'style' => $block['style'],
                'compiled' => $block['compiled'],
                'since' => empty($block['since']) ? null : (int)$block['since'],
                'till' => empty($block['till']) ? null : (int)$block['till'],
                'attributes' => [],
            ];
        }

        foreach ($this->gateway->loadAttributesAssignedToPage($page['id']) as $attribute) {
            $page['zones'][$attribute['zone_id']]['blocks'][$attribute['block_id']]['attributes'][$attribute['id']] = [
                'id' => $attribute['id'],
                'name' => $attribute['name'],
                'value' => $attribute['value'],
            ];
        }

        return $page;
    }

    public function loadPagesMappedToContent(int $contentId, int $versionNo, array $languageCodes): array
    {
        return $this->gateway->loadPagesMappedToContent(
            $contentId,
            $versionNo,
            $languageCodes
        );
    }

    public function insertPage(int $contentId, int $versionNo, string $languageCode, array $page): int
    {
        try {
            $existingPage = $this->gateway->loadPageByContentId($contentId, $versionNo, $languageCode);
            $this->removePage((int) $existingPage['id']);
        } catch (PageNotFoundException $e) {
        }

        $pageId = $this->gateway->insertPage($contentId, $versionNo, $languageCode, $page['layout']);

        foreach ($page['zones'] as $zone) {
            $zoneId = $this->insertZone($zone);
            $this->gateway->assignZoneToPage($zoneId, $pageId);
        }

        return $pageId;
    }

    public function removePage(int $pageId): void
    {
        $removedBlocks = [];
        $removedZones = [];
        $zonesToRemove = $this->gateway->loadZonesAssignedToPage($pageId);

        foreach ($this->gateway->loadAttributesAssignedToPage($pageId) as $attribute) {
            $this->gateway->unassignAttributeFromBlock((int) $attribute['id'], (int) $attribute['block_id']);
            $this->gateway->removeAttribute((int) $attribute['id']);

            if (!\in_array($attribute['block_id'], $removedBlocks, true)) {
                $this->gateway->unassignBlockFromZone((int) $attribute['block_id'], (int) $attribute['zone_id']);
                $this->gateway->removeBlock((int) $attribute['block_id']);
                $this->gateway->removeBlockDesign((int) $attribute['block_id']);
                $this->gateway->removeBlockVisibility((int) $attribute['block_id']);
                $removedBlocks[] = $attribute['block_id'];
            }

            if (!\in_array($attribute['zone_id'], $removedZones, true)) {
                $this->gateway->unassignZoneFromPage((int) $attribute['zone_id'], $pageId);
                $this->gateway->removeZone((int) $attribute['zone_id']);
                $removedZones[] = (int) $attribute['zone_id'];
            }
        }

        foreach ($zonesToRemove as $zone) {
            if (!\in_array($zone['id'], $removedZones, true)) {
                $this->gateway->unassignZoneFromPage((int) $zone['id'], $pageId);
                $this->gateway->removeZone((int) $zone['id']);
            }
        }

        $this->gateway->removePage($pageId);
    }

    private function insertZone(array $zone): ?int
    {
        $zoneId = $this->gateway->insertZone($zone['name']);

        foreach ($zone['blocks'] as $block) {
            $blockId = $this->insertBlock($block);
            $this->gateway->assignBlockToZone($blockId, $zoneId);
        }

        return $zoneId;
    }

    private function insertBlock(array $block): ?int
    {
        $blockId = $this->gateway->insertBlock($block['type'], $block['name'], $block['view']);

        $this->gateway->insertBlockDesign(
            $blockId,
            $block['style'] ?? null,
            $block['compiled'] ?? null,
            $block['class'] ?? null
        );

        $this->gateway->insertBlockVisibility(
            $blockId,
            $block['since'] ?? null,
            $block['till'] ?? null
        );

        foreach ($block['attributes'] as $attribute) {
            $value = null !== $attribute['value'] ? (string) $attribute['value'] : null;
            $attributeId = $this->gateway->insertAttribute($attribute['name'], $value);
            $this->gateway->assignAttributeToBlock($attributeId, $blockId);
        }

        return $blockId;
    }
}
