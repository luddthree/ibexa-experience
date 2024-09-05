<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Twig;

use Ibexa\Bundle\ProductCatalog\Twig\ProductPermissionExtension;
use Ibexa\Bundle\ProductCatalog\Twig\ProductPermissionRuntime;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\PolicyInterface;
use Ibexa\Contracts\ProductCatalog\PermissionResolverInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\ProductCatalog\Local\Repository\Values\Product;
use Twig\RuntimeLoader\RuntimeLoaderInterface;
use Twig\Test\IntegrationTestCase;

final class ProductPermissionExtensionTest extends IntegrationTestCase
{
    /**
     * @return \Twig\Extension\ExtensionInterface[]
     */
    public function getExtensions(): array
    {
        return [
            new ProductPermissionExtension(),
        ];
    }

    /**
     * @return \Twig\RuntimeLoader\RuntimeLoaderInterface[]
     */
    public function getRuntimeLoaders(): array
    {
        return [
            new class($this->getPermissionResolver()) implements RuntimeLoaderInterface {
                public PermissionResolverInterface $permissionResolver;

                public function __construct(PermissionResolverInterface $permissionResolver)
                {
                    $this->permissionResolver = $permissionResolver;
                }

                public function load(string $class): ?ProductPermissionRuntime
                {
                    if (ProductPermissionRuntime::class === $class) {
                        return new ProductPermissionRuntime($this->permissionResolver);
                    }

                    return null;
                }
            },
        ];
    }

    public function getFixturesDir(): string
    {
        return __DIR__ . '/Fixtures/ProductPermissionExtension';
    }

    public function getProduct(string $code): ProductInterface
    {
        $content = new Content();

        return new Product(
            $this->createMock(ProductTypeInterface::class),
            $content,
            $code
        );
    }

    private function getPermissionResolver(): PermissionResolverInterface
    {
        $permissionResolver = $this->createMock(PermissionResolverInterface::class);
        $permissionResolver
            ->expects(self::atLeastOnce())
            ->method('canUser')
            ->willReturnCallback(static function (PolicyInterface $policy): bool {
                /** @var \Ibexa\Contracts\ProductCatalog\Permission\Policy\Product\Delete $policy */
                if ($policy->getObject() === null) {
                    return true;
                }
                $productCode = $policy->getObject()->getCode();

                return $productCode === 'product';
            });

        return $permissionResolver;
    }
}
