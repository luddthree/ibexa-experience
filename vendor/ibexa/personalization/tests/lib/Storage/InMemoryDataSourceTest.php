<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Storage;

use Ibexa\Contracts\Personalization\Storage\DataSourceInterface;
use Ibexa\Contracts\Personalization\Value\ItemListInterface;
use Ibexa\Personalization\Storage\InMemoryDataSource;

final class InMemoryDataSourceTest extends AbstractItemTestCase
{
    protected function createDataSource(ItemListInterface $itemList): DataSourceInterface
    {
        return new InMemoryDataSource($itemList);
    }
}
