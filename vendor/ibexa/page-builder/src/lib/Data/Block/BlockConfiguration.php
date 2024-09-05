<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Data\Block;

use DateTime;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\PageBuilder\Data\Attribute\Attribute;

class BlockConfiguration
{
    /** @var string */
    private $id;

    /** @var string|null */
    private $name;

    /** @var string */
    private $type;

    /** @var string */
    private $view;

    /** @var \Ibexa\PageBuilder\Data\Attribute\Attribute[] */
    private $attributes;

    /** @var string|null */
    private $class;

    /** @var string|null */
    private $style;

    /** @var \DateTime|null */
    private $since;

    /** @var \DateTime|null */
    private $till;

    /**
     * @param string $id
     * @param string|null $name
     * @param string $type
     * @param string $view
     * @param string|null $class
     * @param string|null $style
     * @param \DateTime|null $since
     * @param \DateTime|null $till
     * @param \Ibexa\PageBuilder\Data\Attribute\Attribute[] $attributes
     */
    public function __construct(
        string $id = '',
        ?string $name = '',
        string $type = '',
        string $view = '',
        ?string $class = '',
        ?string $style = '',
        ?DateTime $since = null,
        ?DateTime $till = null,
        array $attributes = []
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->view = $view;
        $this->class = $class;
        $this->style = $style;
        $this->since = $since;
        $this->till = $till;
        $this->attributes = $attributes;
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
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getView(): string
    {
        return $this->view;
    }

    /**
     * @param string $view
     */
    public function setView(string $view): void
    {
        $this->view = $view;
    }

    /**
     * @return \Ibexa\PageBuilder\Data\Attribute\Attribute[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param \Ibexa\PageBuilder\Data\Attribute\Attribute[] $attributes
     */
    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    /**
     * @param string $name
     *
     * @return \Ibexa\PageBuilder\Data\Attribute\Attribute
     */
    public function getAttribute(string $name): ?Attribute
    {
        foreach ($this->attributes as $attribute) {
            if ($attribute->getName() === $name) {
                return $attribute;
            }
        }

        return null;
    }

    /**
     * @param string $name
     * @param mixed $value
     *
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentException
     */
    public function setAttribute($name, $value): void
    {
        if (!isset($this->attributes[$name])) {
            throw new InvalidArgumentException('name', 'attribute "' . $name . '" does not exist');
        }

        $this->attributes[$name] = $value;
    }

    /**
     * @return string
     */
    public function getClass(): ?string
    {
        return $this->class;
    }

    /**
     * @param string $class
     */
    public function setClass(?string $class): void
    {
        $this->class = $class;
    }

    /**
     * @return string
     */
    public function getStyle(): ?string
    {
        return $this->style;
    }

    /**
     * @param string $style
     */
    public function setStyle(?string $style): void
    {
        $this->style = $style;
    }

    /**
     * @return \DateTime
     */
    public function getSince(): ?DateTime
    {
        return $this->since;
    }

    /**
     * @param \DateTime $since
     */
    public function setSince(?DateTime $since): void
    {
        $this->since = $since;
    }

    /**
     * @return \DateTime
     */
    public function getTill(): ?DateTime
    {
        return $this->till;
    }

    /**
     * @param \DateTime $till
     */
    public function setTill(?DateTime $till): void
    {
        $this->till = $till;
    }
}

class_alias(BlockConfiguration::class, 'EzSystems\EzPlatformPageBuilder\Data\Block\BlockConfiguration');
