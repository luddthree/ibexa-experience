<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\Common\IndexDataProvider;

use Ibexa\Contracts\Core\Persistence\Content as SPIContent;

interface IndexDataProviderInterface
{
    public function isSupported(SPIContent $content): bool;

    /**
     * @return \Ibexa\Contracts\Core\Search\Field[]
     */
    public function getSearchData(SPIContent $content): array;
}
