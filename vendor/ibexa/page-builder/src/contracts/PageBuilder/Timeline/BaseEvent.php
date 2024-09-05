<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\PageBuilder\PageBuilder\Timeline;

use DateTimeInterface;
use Ibexa\Contracts\PageBuilder\Timeline\EventInterface;

abstract class BaseEvent implements EventInterface
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $description;

    /** @var \DateTimeInterface */
    protected $date;

    /** @var string */
    protected $icon;

    /**
     * @param string $name
     * @param string $description
     * @param \DateTimeInterface $date
     * @param string $icon
     */
    public function __construct(
        string $name,
        string $description,
        \DateTimeInterface $date,
        string $icon
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->date = $date;
        $this->icon = $icon;
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
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    /**
     * @param \DateTimeInterface $date
     */
    public function setDate(DateTimeInterface $date): void
    {
        $this->date = $date;
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     */
    public function setIcon(string $icon): void
    {
        $this->icon = $icon;
    }
}

class_alias(BaseEvent::class, 'EzSystems\EzPlatformPageBuilder\PageBuilder\Timeline\BaseEvent');
