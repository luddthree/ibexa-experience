<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Twig;

use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use JMS\TranslationBundle\Annotation\Ignore;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\RuntimeExtensionInterface;

final class ProductTypeRuntime implements RuntimeExtensionInterface
{
    private const TYPE_VIRTUAL = 'product_type.virtual';
    private const TYPE_PHYSICAL = 'product_type.physical';

    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getType(ProductTypeInterface $productType): string
    {
        if ($productType->isVirtual()) {
            return $this->getTranslation(self::TYPE_VIRTUAL);
        }

        return $this->getTranslation(self::TYPE_PHYSICAL);
    }

    private function getTranslation(string $id): string
    {
        return $this->translator->trans(
            /** @Ignore */
            $id,
            [],
            'ibexa_product_catalog'
        );
    }
}
