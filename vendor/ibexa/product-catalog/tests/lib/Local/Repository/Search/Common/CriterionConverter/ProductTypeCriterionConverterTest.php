<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Repository\Search\Common\CriterionConverter;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductType;
use Ibexa\ProductCatalog\Local\Repository\Search\Common\CriterionConverter\ProductTypeCriterionConverter;
use PHPUnit\Framework\TestCase;

final class ProductTypeCriterionConverterTest extends TestCase
{
    public function testConvert(): void
    {
        $converter = new ProductTypeCriterionConverter();

        self::assertEquals(
            new ContentTypeIdentifier(['foo', 'bar', 'baz']),
            $converter->convert(
                new ProductType(['foo', 'bar', 'baz'])
            )
        );
    }
}
