<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data;

use Ibexa\Bundle\ProductCatalog\Validator\Constraints\UniqueCustomerGroupIdentifier;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @UniqueCustomerGroupIdentifier
 */
final class CustomerGroupUpdateData implements TranslationContainerInterface
{
    private int $id;

    private Language $language;

    /**
     * @Assert\Length(min=1, max=64)
     * @Assert\Regex(pattern="/^[[:alnum:]_]+$/", message="ibexa.customer_group.identifier.pattern")
     */
    private ?string $identifier;

    /**
     * @Assert\Length(min=1, max=190)
     */
    private ?string $name;

    /**
     * @Assert\Length(max=10000)
     */
    private ?string $description;

    /**
     * @Assert\Range(min=-100)
     *
     * @var numeric-string|null
     */
    private ?string $globalPriceRate;

    /**
     * @param numeric-string|null $globalPriceRate
     */
    public function __construct(
        int $id,
        Language $language,
        ?string $identifier = null,
        ?string $name = null,
        ?string $description = null,
        ?string $globalPriceRate = null
    ) {
        $this->id = $id;
        $this->language = $language;
        $this->identifier = $identifier;
        $this->name = $name;
        $this->description = $description;
        $this->globalPriceRate = $globalPriceRate;
    }

    public static function createFromCustomerGroup(CustomerGroupInterface $customerGroup, Language $language): self
    {
        return new self(
            $customerGroup->getId(),
            $language,
            $customerGroup->getIdentifier(),
            $customerGroup->getName(),
            $customerGroup->getDescription(),
            $customerGroup->getGlobalPriceRate(),
        );
    }

    public function getLanguage(): Language
    {
        return $this->language;
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return $this
     */
    public function setIdentifier(?string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return $this
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return $this
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param numeric-string|null $globalPriceRate
     *
     * @return $this
     */
    public function setGlobalPriceRate(?string $globalPriceRate): self
    {
        $this->globalPriceRate = $globalPriceRate;

        return $this;
    }

    /**
     * @return numeric-string|null
     */
    public function getGlobalPriceRate(): ?string
    {
        return $this->globalPriceRate;
    }

    public static function getTranslationMessages(): array
    {
        return [
            Message::create('ibexa.customer_group.identifier.pattern', 'validators')
                ->setDesc(
                    'Customer Group identifier may only contain letters from "a" to "z", numbers and underscores.'
                ),
        ];
    }
}
