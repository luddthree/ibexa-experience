<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data\Catalog;

use Ibexa\Bundle\ProductCatalog\Validator\Constraints\UniqueCatalogIdentifier;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\LogicalAnd;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @UniqueCatalogIdentifier
 */
final class CatalogCreateData
{
    /**
     * @Assert\NotBlank()
     */
    private ?Language $language;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=1, max=64)
     * @Assert\Regex("/^\w+$/")
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

    private ?LogicalAnd $criteria = null;

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

    public function getCriteria(): ?LogicalAnd
    {
        return $this->criteria;
    }

    public function setCriteria(?LogicalAnd $criteria): void
    {
        $this->criteria = $criteria;
    }
}
