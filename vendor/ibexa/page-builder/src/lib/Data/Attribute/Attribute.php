<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Data\Attribute;

class Attribute
{
    /** @var string */
    private $id;

    /** @var string */
    private $name;

    /** @var string */
    private $value;

    /**
     * @param string $id
     * @param string $name
     * @param string $value
     */
    public function __construct(
        string $id = '',
        string $name = '',
        $value = ''
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->value = $value;
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
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }
}

class_alias(Attribute::class, 'EzSystems\EzPlatformPageBuilder\Data\Attribute\Attribute');
