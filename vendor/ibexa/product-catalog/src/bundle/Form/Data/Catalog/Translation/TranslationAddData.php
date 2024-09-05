<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data\Catalog\Translation;

use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class TranslationAddData
{
    /**
     * @var \Ibexa\Contracts\ProductCatalog\Values\CatalogInterface&\Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface
     */
    private CatalogInterface $catalog;

    /**
     * @Assert\NotBlank()
     */
    private ?Language $language;

    private ?Language $baseLanguage;

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\CatalogInterface&\Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface $catalog
     */
    public function __construct(
        CatalogInterface $catalog,
        ?Language $language = null,
        ?Language $baseLanguage = null
    ) {
        $this->catalog = $catalog;
        $this->language = $language;
        $this->baseLanguage = $baseLanguage;
    }

    /**
     * @return \Ibexa\Contracts\ProductCatalog\Values\CatalogInterface&\Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface
     */
    public function getCatalog(): CatalogInterface
    {
        return $this->catalog;
    }

    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    public function setLanguage(Language $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getBaseLanguage(): ?Language
    {
        return $this->baseLanguage;
    }

    public function setBaseLanguage(Language $baseLanguage): self
    {
        $this->baseLanguage = $baseLanguage;

        return $this;
    }
}
