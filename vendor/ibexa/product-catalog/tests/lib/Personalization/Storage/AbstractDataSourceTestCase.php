<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Personalization\Storage;

use Ibexa\Tests\ProductCatalog\Personalization\Creator\DataSourceTestItemFactory;
use PHPUnit\Framework\TestCase;

abstract class AbstractDataSourceTestCase extends TestCase
{
    protected DataSourceTestItemFactory $itemCreator;

    /**
     * @param array<mixed> $data
     */
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->itemCreator = new DataSourceTestItemFactory();
    }
}
