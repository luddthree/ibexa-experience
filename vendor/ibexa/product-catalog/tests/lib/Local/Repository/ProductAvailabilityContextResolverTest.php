<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Repository;

use Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityContextInterface;
use Ibexa\ProductCatalog\Local\Repository\ProductAvailabilityContextResolver;
use Ibexa\ProductCatalog\Local\Repository\Values\AvailabilityContext\AvailabilityContext;
use PHPUnit\Framework\TestCase;

final class ProductAvailabilityContextResolverTest extends TestCase
{
    public function testResolve(): void
    {
        $availabilityContext = $this->getAvailabilityContext();
        $resolver = new ProductAvailabilityContextResolver();
        $resolvedContext = $resolver->resolve($availabilityContext);

        self::assertSame($resolvedContext, $availabilityContext);
    }

    public function testResolveDefault(): void
    {
        $resolver = new ProductAvailabilityContextResolver();
        $resolvedContext = $resolver->resolve();

        self::assertEquals($resolvedContext, new AvailabilityContext());
    }

    private function getAvailabilityContext(): AvailabilityContextInterface
    {
        return new class() implements AvailabilityContextInterface {
            /* empty implementation */
        };
    }
}
