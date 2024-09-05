<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Dispatcher;

use Ibexa\Contracts\ProductCatalog\ProductTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\LanguageSettings;
use Ibexa\Contracts\ProductCatalog\Values\ProductType\ProductTypeListInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductType\ProductTypeQuery;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;

/**
 * @extends \Ibexa\ProductCatalog\Dispatcher\AbstractServiceDispatcher<
 *     \Ibexa\Contracts\ProductCatalog\ProductTypeServiceInterface
 * >
 */
final class ProductTypeServiceDispatcher extends AbstractServiceDispatcher implements ProductTypeServiceInterface
{
    public function getProductType(
        string $identifier,
        ?LanguageSettings $languageSettings = null
    ): ProductTypeInterface {
        return $this->dispatch()->getProductType($identifier, $languageSettings);
    }

    public function findProductTypes(
        ?ProductTypeQuery $query = null,
        ?LanguageSettings $languageSettings = null
    ): ProductTypeListInterface {
        return $this->dispatch()->findProductTypes($query, $languageSettings);
    }
}
