<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data;

use Ibexa\Bundle\ProductCatalog\Validator\Constraints\UniqueAttributeGroupIdentifier;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @UniqueAttributeGroupIdentifier
 */
abstract class AbstractAttributeGroupData implements TranslationContainerInterface
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=64)
     * @Assert\Regex(pattern="/^[[:alnum:]_]+$/", message="ibexa.attribute_group.identifier.pattern")
     */
    private ?string $identifier;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=190)
     */
    private ?string $name;

    /**
     * @Assert\PositiveOrZero()
     * @Assert\NotBlank()
     */
    private ?int $position;

    /**
     * @Assert\NotBlank()
     */
    private ?Language $language;

    public function __construct(
        ?string $identifier = null,
        ?string $name = null,
        ?Language $language = null,
        ?int $position = 0
    ) {
        $this->identifier = $identifier;
        $this->name = $name;
        $this->language = $language;
        $this->position = $position;
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

    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    public function setLanguage(?Language $language): void
    {
        $this->language = $language;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }

    public static function getTranslationMessages(): array
    {
        return [
            Message::create('ibexa.attribute_group.identifier.pattern', 'validators')
                ->setDesc(
                    'Attribute Group identifier may only contain letters from "a" to "z", numbers and underscores.'
                ),
        ];
    }
}
