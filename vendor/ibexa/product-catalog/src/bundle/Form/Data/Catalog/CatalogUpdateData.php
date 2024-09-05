<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data\Catalog;

use Ibexa\Bundle\ProductCatalog\Validator\Constraints\UniqueCatalogIdentifier;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @UniqueCatalogIdentifier
 */
final class CatalogUpdateData
{
    private int $id;

    private Language $language;

    /**
     * @Assert\Length(min=1, max=64)
     * @Assert\Regex("/^\w+$/")
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

    private ?CriterionInterface $criteria;

    public function __construct(
        int $id,
        Language $language,
        ?string $identifier = null,
        ?string $name = null,
        ?string $description = null,
        ?CriterionInterface $criteria = null
    ) {
        $this->id = $id;
        $this->language = $language;
        $this->identifier = $identifier;
        $this->name = $name;
        $this->description = $description;
        $this->criteria = $criteria;
    }

    public static function createFromCatalog(CatalogInterface $catalog, Language $language): self
    {
        return new self(
            $catalog->getId(),
            $language,
            $catalog->getIdentifier(),
            $catalog->getName(),
            $catalog->getDescription(),
            $catalog->getQuery(),
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

    public function getCriteria(): ?CriterionInterface
    {
        return $this->criteria;
    }

    public function setCriteria(?CriterionInterface $criteria): void
    {
        $this->criteria = $criteria;
    }
}
