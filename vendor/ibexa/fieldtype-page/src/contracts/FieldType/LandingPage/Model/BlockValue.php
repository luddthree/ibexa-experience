<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model;

use DateTime;
use DateTimeInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;

class BlockValue
{
    /** @var string */
    private $id;

    /** @var non-empty-string */
    private $type;

    /** @var string|null */
    private $name;

    /** @var string */
    private $view;

    /** @var string|null */
    private $class;

    /** @var string|null */
    private $style;

    /** @var string|null */
    private $compiled;

    /** @var \DateTime|null */
    private $since;

    /** @var \DateTime|null */
    private $till;

    /** @var \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Attribute[] */
    private $attributes;

    /**
     * @param string $id
     * @param non-empty-string $type
     * @param string|null $name
     * @param string $view
     * @param string|null $class
     * @param string|null $style
     * @param string|null $compiled
     * @param \DateTime|null $since
     * @param \DateTime|null $till
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Attribute[] $attributes
     */
    public function __construct(
        string $id,
        string $type,
        ?string $name,
        string $view,
        ?string $class,
        ?string $style,
        ?string $compiled,
        ?DateTime $since,
        ?DateTime $till,
        array $attributes
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->name = $name;
        $this->view = $view;
        $this->class = $class;
        $this->style = $style;
        $this->compiled = $compiled;
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
     * @return non-empty-string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param non-empty-string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
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
     * @return \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Attribute[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Attribute[] $attributes
     */
    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    /**
     * @param string $name
     *
     * @return \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Attribute
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
     * @todo fix
     *
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
     * @return string|null
     */
    public function getClass(): ?string
    {
        return $this->class;
    }

    /**
     * @param string|null $class
     */
    public function setClass(?string $class): void
    {
        $this->class = $class;
    }

    /**
     * @return string|null
     */
    public function getStyle(): ?string
    {
        return $this->style;
    }

    /**
     * @param string|null $style
     */
    public function setStyle(?string $style): void
    {
        $this->style = $style;
    }

    /**
     * @return string|null
     */
    public function getCompiled(): ?string
    {
        return $this->compiled;
    }

    /**
     * @param string|null $compiled
     */
    public function setCompiled(?string $compiled): void
    {
        $this->compiled = $compiled;
    }

    /**
     * @return \DateTime|null
     */
    public function getSince(): ?DateTime
    {
        return $this->since;
    }

    /**
     * @param \DateTime|null $since
     */
    public function setSince(?DateTime $since): void
    {
        $this->since = $since;
    }

    /**
     * @return \DateTime|null
     */
    public function getTill(): ?DateTime
    {
        return $this->till;
    }

    /**
     * @param \DateTime|null $till
     */
    public function setTill(?DateTime $till): void
    {
        $this->till = $till;
    }

    /**
     * @param \DateTimeInterface|null $dateTime
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function isVisible(DateTimeInterface $dateTime = null): bool
    {
        $dateTime = $dateTime ?? new DateTime();

        return (null === $this->getSince() || $this->getSince() <= $dateTime) &&
               (null === $this->getTill() || $this->getTill() > $dateTime);
    }
}

class_alias(BlockValue::class, 'EzSystems\EzPlatformPageFieldType\FieldType\LandingPage\Model\BlockValue');
