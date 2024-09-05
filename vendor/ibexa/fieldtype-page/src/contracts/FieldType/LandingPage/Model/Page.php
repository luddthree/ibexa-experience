<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model;

use Ibexa\FieldTypePage\Exception\BlockNotFoundException;
use Ibexa\FieldTypePage\Exception\ZoneNotFoundException;

/**
 * Represents page.
 *
 * Page contains collection of zones
 */
class Page
{
    /** @var string */
    private $layout;

    /** @var \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Zone[] */
    private $zones;

    /**
     * @param string $layout
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Zone[] $zones
     */
    public function __construct(string $layout = '', array $zones = [])
    {
        $this->layout = $layout;
        $this->zones = $zones;
    }

    /**
     * @return string
     */
    public function getLayout(): string
    {
        return $this->layout;
    }

    /**
     * @param string $layout
     */
    public function setLayout(string $layout): void
    {
        $this->layout = $layout;
    }

    /**
     * @return \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Zone[]
     */
    public function getZones(): array
    {
        return $this->zones;
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Zone[] $zones
     */
    public function setZones(array $zones): void
    {
        $this->zones = $zones;
    }

    /**
     * @param $id
     *
     * @return \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Zone
     *
     * @throws \Ibexa\FieldTypePage\Exception\ZoneNotFoundException
     */
    public function getZoneByIdentifier($id): Zone
    {
        foreach ($this->zones as $zone) {
            if ($id === $zone->getId()) {
                return $zone;
            }
        }

        throw new ZoneNotFoundException('Zone not found (id: ' . $id . ')');
    }

    /**
     * @throws \Ibexa\FieldTypePage\Exception\ZoneNotFoundException
     */
    public function getZoneByName(string $name): Zone
    {
        foreach ($this->zones as $zone) {
            if ($name === $zone->getName()) {
                return $zone;
            }
        }

        throw new ZoneNotFoundException('Zone not found (name: ' . $name . ')');
    }

    /**
     * Returns block by its ID.
     *
     * @param $id
     *
     * @return \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue
     *
     * @throws \Ibexa\FieldTypePage\Exception\BlockNotFoundException
     */
    public function getBlockById($id): BlockValue
    {
        foreach ($this->getZones() as $zone) {
            foreach ($zone->getBlocks() as $block) {
                if ($block->getId() === $id) {
                    return $block;
                }
            }
        }

        throw new BlockNotFoundException('Block not found (id: ' . $id . ')');
    }

    /**
     * @return \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue[]
     */
    public function getBlockIterator(): iterable
    {
        foreach ($this->getZones() as $zone) {
            foreach ($zone->getBlocks() as $block) {
                yield $block;
            }
        }
    }
}

class_alias(Page::class, 'EzSystems\EzPlatformPageFieldType\FieldType\LandingPage\Model\Page');
