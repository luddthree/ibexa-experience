<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Twig;

use Ibexa\Bundle\ProductCatalog\Twig\AttributeDefinitionPermissionExtension;
use Ibexa\Bundle\ProductCatalog\Twig\AttributeDefinitionPermissionRuntime;
use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeDefinition\AttributeDefinitionCreateStruct;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\PolicyInterface;
use Ibexa\Contracts\ProductCatalog\PermissionResolverInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinition;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeGroup;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeType;
use Twig\RuntimeLoader\RuntimeLoaderInterface;
use Twig\Test\IntegrationTestCase;

final class AttributeDefinitionPermissionExtensionTest extends IntegrationTestCase
{
    /**
     * @return \Twig\Extension\ExtensionInterface[]
     */
    public function getExtensions(): array
    {
        return [
            new AttributeDefinitionPermissionExtension(),
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

                public function load(string $class): ?AttributeDefinitionPermissionRuntime
                {
                    if (AttributeDefinitionPermissionRuntime::class === $class) {
                        return new AttributeDefinitionPermissionRuntime($this->permissionResolver);
                    }

                    return null;
                }
            },
        ];
    }

    public function getFixturesDir(): string
    {
        return __DIR__ . '/Fixtures/AttributeDefinitionPermissionExtension';
    }

    public function getAttributeDefinition(string $identifier): AttributeDefinition
    {
        return new AttributeDefinition(
            1,
            $identifier,
            new AttributeType('integer'),
            new AttributeGroup(1, 'foo', 'Foo', 0, [], []),
            'name',
            0,
            [],
            'description',
            [],
            [],
        );
    }

    public function getAttributeDefinitionCreateStruct(string $identifier): AttributeDefinitionCreateStruct
    {
        return new AttributeDefinitionCreateStruct($identifier);
    }

    private function getPermissionResolver(): PermissionResolverInterface
    {
        $permissionResolver = $this->createMock(PermissionResolverInterface::class);
        $permissionResolver
            ->expects(self::atLeastOnce())
            ->method('canUser')
            ->willReturnCallback(static function (PolicyInterface $policy): bool {
                /** @var \Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface|null $attributeDefinition */
                $attributeDefinition = $policy->getObject();
                if ($attributeDefinition === null) {
                    return true;
                }

                return 'attribute_definition' === $attributeDefinition->getIdentifier();
            });

        return $permissionResolver;
    }
}
