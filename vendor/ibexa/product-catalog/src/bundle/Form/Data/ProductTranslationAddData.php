<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data;

use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Symfony\Component\Validator\Constraints as Assert;

final class ProductTranslationAddData
{
    /**
     * @Assert\NotBlank()
     */
    private ?Language $language;

    private ?Language $baseLanguage;

    public function __construct(?Language $language = null, ?Language $baseLanguage = null)
    {
        $this->language = $language;
        $this->baseLanguage = $baseLanguage;
    }

    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    public function setLanguage(?Language $language): void
    {
        $this->language = $language;
    }

    public function getBaseLanguage(): ?Language
    {
        return $this->baseLanguage;
    }

    public function setBaseLanguage(?Language $baseLanguage): void
    {
        $this->baseLanguage = $baseLanguage;
    }
}
