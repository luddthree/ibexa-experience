<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\UI\Completeness\Task;

use Ibexa\Bundle\ProductCatalog\UI\Completeness\Entry\BooleanEntry;
use Ibexa\Bundle\ProductCatalog\UI\Completeness\Entry\EntryInterface;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\ProductCatalog\AssetServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AssetsTask extends AbstractTask
{
    private AssetServiceInterface $assetService;

    private TranslatorInterface $translator;

    public function __construct(
        AssetServiceInterface $assetService,
        TranslatorInterface $translator
    ) {
        $this->assetService = $assetService;
        $this->translator = $translator;
    }

    public function getName(): string
    {
        return $this->translator->trans(/** @Desc("Assets") */ 'product.completeness.assets.label', [], 'ibexa_product_catalog');
    }

    public function getIdentifier(): string
    {
        return 'assets';
    }

    public function getEntry(ProductInterface $product): ?EntryInterface
    {
        try {
            $assets = $this->assetService->findAssets($product);
        } catch (UnauthorizedException $e) {
            return null;
        }

        return new BooleanEntry($assets->count() > 0);
    }

    /**
     * @return array<\Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\TaskGroup>|null
     */
    public function getSubtaskGroups(ProductInterface $product): ?array
    {
        return null;
    }

    /**
     * @phpstan-return int<1, max>
     */
    public function getWeight(): int
    {
        return 1;
    }

    public function getEditUrl(ProductInterface $product): ?string
    {
        return null;
    }
}
