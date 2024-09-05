<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\GraphQL\Strategy;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface;
use Ibexa\FieldTypeMatrix\GraphQL\Strategy\ContentResolvingStrategyInterface;

final class ProductContentResolvingStrategy implements ContentResolvingStrategyInterface
{
    public function resolveContent(object $item): Content
    {
        /** @var \Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface $item */
        return $item->getContent();
    }

    public function supports(object $item): bool
    {
        return $item instanceof ContentAwareProductInterface;
    }
}
