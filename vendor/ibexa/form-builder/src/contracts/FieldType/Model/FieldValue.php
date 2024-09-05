<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\FormBuilder\FieldType\Model;

class FieldValue
{
    /** @var string */
    private $name;

    /** @var mixed */
    private $value;

    /** @var string */
    private $identifier;

    /** @var string */
    private $displayValue;

    /**
     * @param string $identifier
     * @param string $name
     * @param mixed|null $value
     * @param string|null $displayValue
     */
    public function __construct(
        string $identifier,
        string $name,
        $value = null,
        ?string $displayValue = null
    ) {
        $this->name = $name;
        $this->value = $value;
        $this->identifier = $identifier;
        $this->displayValue = $displayValue;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return mixed|null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return string|null
     */
    public function getDisplayValue(): ?string
    {
        return $this->displayValue;
    }
}

class_alias(FieldValue::class, 'EzSystems\EzPlatformFormBuilder\FieldType\Model\FieldValue');
