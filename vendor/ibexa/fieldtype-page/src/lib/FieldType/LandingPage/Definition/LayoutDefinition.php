<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FieldTypePage\FieldType\LandingPage\Definition;

/**
 * Definition of page layout.
 */
class LayoutDefinition
{
    /**
     * Page layout identifier.
     *
     * @var string
     */
    private $id;

    /**
     * Page layout name.
     *
     * @var string
     */
    private $name;

    /**
     * Page layout description.
     *
     * @var string
     */
    private $description;

    /**
     * Path to layout thumbnail.
     *
     * @var string
     */
    private $thumbnail;

    /**
     * Template path.
     *
     * @var string
     */
    private $template;

    /**
     * List of zones in layout.
     *
     * @var array
     */
    private $zones;

    /** @var bool */
    private $visible;

    /**
     * @param string $id
     * @param string $name
     * @param string $description
     * @param string $thumbnail
     * @param string $template
     * @param array $zones
     * @param bool $visible
     */
    public function __construct(
        $id,
        $name,
        $description,
        $thumbnail,
        $template,
        array $zones,
        bool $visible = true
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->thumbnail = $thumbnail;
        $this->template = $template;
        $this->zones = $zones;
        $this->visible = $visible;
    }

    /**
     * Returns layout identifier.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns layout name.
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns layout description.
     *
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Returns layout thumbnail.
     *
     * @return mixed
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * Returns template path.
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Returns zones list.
     *
     * @return array
     */
    public function getZones()
    {
        return $this->zones;
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }
}

class_alias(LayoutDefinition::class, 'EzSystems\EzPlatformPageFieldType\FieldType\LandingPage\Definition\LayoutDefinition');
