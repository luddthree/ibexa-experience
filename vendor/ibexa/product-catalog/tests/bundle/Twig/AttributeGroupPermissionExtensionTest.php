<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Twig;

use Ibexa\Bundle\ProductCatalog\Twig\AttributeGroupPermissionExtension;
use Ibexa\Bundle\ProductCatalog\Twig\AttributeGroupPermissionRuntime;
use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeGroup\AttributeGroupCreateStruct;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\PolicyInterface;
use Ibexa\Contracts\ProductCatalog\PermissionResolverInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeGroup;
use Twig\RuntimeLoader\RuntimeLoaderInterface;
use Twig\Test\IntegrationTestCase;

final class AttributeGroupPermissionExtensionTest extends IntegrationTestCase
{
    /**
     * @return \Twig\Extension\ExtensionInterface[]
     */
    public function getExtensions(): array
    {
        return [
            new AttributeGroupPermissionExtension(),
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

                public function load(string $class): ?AttributeGroupPermissionRuntime
                {
                    if (AttributeGroupPermissionRuntime::class === $class) {
                        return new AttributeGroupPermissionRuntime($this->permissionResolver);
                    }

                    return null;
                }
            },
        ];
    }

    public function getFixturesDir(): string
    {
        return __DIR__ . '/Fixtures/AttributeGroupPermissionExtension';
    }

    public function getAttributeGroup(string $identifier): AttributeGroup
    {
        return new AttributeGroup(1, $identifier, 'name', 0, [], []);
    }

    public function getAttributeGroupCreateStruct(string $identifier): AttributeGroupCreateStruct
    {
        return new AttributeGroupCreateStruct($identifier);
    }

    private function getPermissionResolver(): PermissionResolverInterface
    {
        $permissionResolver = $this->createMock(PermissionResolverInterface::class);
        $permissionResolver
            ->expects(self::atLeastOnce())
            ->method('canUser')
            ->willReturnCallback(static function (PolicyInterface $policy): bool {
                /** @var \Ibexa\Contracts\ProductCatalog\Permission\Policy\AttributeGroup\Delete $policy */
                if ($policy->getObject() === null) {
                    return true;
                }
                $attributeGroupIdentifier = $policy->getObject()->getIdentifier();

                return $attributeGroupIdentifier === 'attribute_group';
            });

        return $permissionResolver;
    }
}
