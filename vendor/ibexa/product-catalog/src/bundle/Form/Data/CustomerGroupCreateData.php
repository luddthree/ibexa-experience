<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data;

use Ibexa\Bundle\ProductCatalog\Validator\Constraints\UniqueCustomerGroupIdentifier;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @UniqueCustomerGroupIdentifier
 */
final class CustomerGroupCreateData implements TranslationContainerInterface
{
    /**
     * @Assert\NotBlank()
     */
    private ?Language $language;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=1, max=64)
     * @Assert\Regex(pattern="/^[[:alnum:]_]+$/", message="ibexa.customer_group.identifier.pattern")
     */
    private string $identifier = '';

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=1, max=150)
     */
    private string $name = '';

    /**
     * @Assert\Length(max=10000)
     */
    private string $description = '';

    /**
     * @Assert\Range(min=-100)
     *
     * @var numeric-string
     */
    private string $globalPriceRate = '0';

    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    public function setLanguage(?Language $language): void
    {
        $this->language = $language;
    }

    /**
     * @return $this
     */
    public function setIdentifier(?string $identifier): self
    {
        $this->identifier = $identifier ?? '';

        return $this;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return $this
     */
    public function setName(?string $name): self
    {
        $this->name = $name ?? '';

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
        $this->description = $description ?? '';

        return $this;
    }

    /**
     * @param numeric-string|null $globalPriceRate
     *
     * @return $this
     */
    public function setGlobalPriceRate(?string $globalPriceRate): self
    {
        $this->globalPriceRate = $globalPriceRate ?? '0';

        return $this;
    }

    /**
     * @return numeric-string
     */
    public function getGlobalPriceRate(): string
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
