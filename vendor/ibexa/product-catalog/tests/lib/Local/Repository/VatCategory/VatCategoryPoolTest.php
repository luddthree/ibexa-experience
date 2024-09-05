<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Repository\VatCategory;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\ProductCatalog\Values\VatCategoryInterface;
use Ibexa\ProductCatalog\Local\Repository\VatCategory\VatCategoryPool;
use PHPUnit\Framework\TestCase;

final class VatCategoryPoolTest extends TestCase
{
    public function testGetVatCategory(): void
    {
        $data = $this->getExampleVatCategories();
        $pool = new VatCategoryPool($data);

        self::assertSame(
            $data['foo']['standard'],
            $pool->getVatCategory('foo', 'standard')
        );

        self::assertSame(
            $data['bar']['reduced'],
            $pool->getVatCategory('bar', 'reduced')
        );
    }

    public function testGetVatCategoryThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("Could not find 'Ibexa\Contracts\ProductCatalog\Values\VatCategoryInterface' with identifier 'array (\n  'region' => 'non-existing',\n  'identifier' => 'standard',\n)'");

        $pool = new VatCategoryPool($this->getExampleVatCategories());
        $pool->getVatCategory('non-existing', 'standard');
    }

    public function testGetVatCategories(): void
    {
        $data = $this->getExampleVatCategories();
        $pool = new VatCategoryPool($data);

        self::assertEquals($data['foo'], $pool->getVatCategories('foo'));
    }

    /**
     * @return array<string,array<string,\Ibexa\Contracts\ProductCatalog\Values\VatCategoryInterface>>
     */
    private function getExampleVatCategories(): array
    {
        return [
            'foo' => [
                'standard' => $this->createMock(VatCategoryInterface::class),
                'reduced' => $this->createMock(VatCategoryInterface::class),
            ],
            'bar' => [
                'standard' => $this->createMock(VatCategoryInterface::class),
                'reduced' => $this->createMock(VatCategoryInterface::class),
            ],
        ];
    }
}
