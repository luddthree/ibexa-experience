<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data;

use Ibexa\Bundle\ProductCatalog\Validator\Constraints\UniqueAttributeDefinitionIdentifier;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @UniqueAttributeDefinitionIdentifier
 */
abstract class AbstractAttributeDefinitionData implements TranslationContainerInterface
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=64)
     * @Assert\Regex(pattern="/^[[:alnum:]_]+$/", message="ibexa.attribute_definition.identifier.pattern")
     */
    private ?string $identifier;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=190)
     */
    private ?string $name;

    /**
     * @Assert\Length(max=10000)
     */
    private ?string $description;

    /**
     * @Assert\NotBlank()
     */
    private ?AttributeGroupInterface $attributeGroup;

    /**
     * @Assert\PositiveOrZero()
     * @Assert\NotBlank()
     */
    private ?int $position;

    /**
     * @Assert\NotBlank()
     */
    private ?Language $language;

    /**
     * @var array<string,mixed>
     */
    private array $options;

    /**
     * @param array<string,mixed> $options
     */
    public function __construct(
        ?string $identifier = null,
        ?AttributeGroupInterface $attributeGroup = null,
        ?string $name = null,
        ?string $description = null,
        ?Language $language = null,
        ?int $position = 0,
        array $options = []
    ) {
        $this->identifier = $identifier;
        $this->attributeGroup = $attributeGroup;
        $this->name = $name;
        $this->description = $description;
        $this->language = $language;
        $this->position = $position;
        $this->options = $options;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(?string $identifier): void
    {
        $this->identifier = $identifier;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function hasDescription(): bool
    {
        return $this->description !== null;
    }

    public function getAttributeGroup(): ?AttributeGroupInterface
    {
        return $this->attributeGroup;
    }

    public function setAttributeGroup(?AttributeGroupInterface $attributeGroup): void
    {
        $this->attributeGroup = $attributeGroup;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }

    /**
     * @return array<string,mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array<string,mixed> $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    public function setLanguage(?Language $language): void
    {
        $this->language = $language;
    }

    public static function getTranslationMessages(): array
    {
        return [
            Message::create('ibexa.attribute_definition.identifier.pattern', 'validators')
                ->setDesc('Attribute identifier may only contain letters from "a" to "z", numbers and underscores.'),
        ];
    }
}
