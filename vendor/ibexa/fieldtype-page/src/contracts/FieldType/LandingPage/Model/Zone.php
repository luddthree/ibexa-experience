<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model;

/**
 * Zone is area on the page which contains blocks.
 */
class Zone
{
    /** @var string */
    private $id;

    /** @var string */
    private $name;

    /** @var \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue[] */
    private $blocks;

    /**
     * @param string $id
     * @param string $name Zone name
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue[] $blocks
     */
    public function __construct(string $id, string $name, array $blocks = [])
    {
        $this->id = $id;
        $this->name = $name;
        $this->blocks = $blocks;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue[]
     */
    public function getBlocks(): array
    {
        return $this->blocks;
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue[] $blocks
     */
    public function setBlocks(array $blocks): void
    {
        $this->blocks = $blocks;
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $block
     */
    public function addBlock(BlockValue $block): void
    {
        $this->blocks[] = $block;
    }
}

class_alias(Zone::class, 'EzSystems\EzPlatformPageFieldType\FieldType\LandingPage\Model\Zone');
