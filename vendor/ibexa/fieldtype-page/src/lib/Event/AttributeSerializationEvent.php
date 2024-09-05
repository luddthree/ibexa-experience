<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Event;

use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition;
use Symfony\Contracts\EventDispatcher\Event;

class AttributeSerializationEvent extends Event
{
    /** @var \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue */
    private $blockValue;

    /** @var string */
    private $attributeIdentifier;

    /** @var mixed|null */
    private $serializedValue;

    /** @var mixed */
    private $deserializedValue;

    private ?BlockAttributeDefinition $attributeDefinition;

    public function __construct(
        BlockValue $blockValue,
        string $attributeIdentifier,
        ?BlockAttributeDefinition $attributeDefinition
    ) {
        $this->blockValue = $blockValue;
        $this->attributeIdentifier = $attributeIdentifier;
        $this->attributeDefinition = $attributeDefinition;
    }

    /**
     * @return \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue
     */
    public function getBlockValue(): BlockValue
    {
        return $this->blockValue;
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $blockValue
     */
    public function setBlockValue(BlockValue $blockValue): void
    {
        $this->blockValue = $blockValue;
    }

    /**
     * @return string
     */
    public function getAttributeIdentifier(): string
    {
        return $this->attributeIdentifier;
    }

    public function getAttributeDefinition(): ?BlockAttributeDefinition
    {
        return $this->attributeDefinition;
    }

    /**
     * @param string $attributeIdentifier
     */
    public function setAttributeIdentifier(string $attributeIdentifier): void
    {
        $this->attributeIdentifier = $attributeIdentifier;
    }

    /**
     * @return mixed|null
     */
    public function getSerializedValue()
    {
        return $this->serializedValue;
    }

    /**
     * @param mixed|null $serializedValue
     */
    public function setSerializedValue($serializedValue): void
    {
        $this->serializedValue = $serializedValue;
    }

    /**
     * @return mixed|null
     */
    public function getDeserializedValue()
    {
        return $this->deserializedValue;
    }

    /**
     * @param mixed $deserializedValue
     */
    public function setDeserializedValue($deserializedValue): void
    {
        $this->deserializedValue = $deserializedValue;
    }
}

class_alias(AttributeSerializationEvent::class, 'EzSystems\EzPlatformPageFieldType\Event\AttributeSerializationEvent');
