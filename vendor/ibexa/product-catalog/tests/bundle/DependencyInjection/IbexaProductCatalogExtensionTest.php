<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\DependencyInjection;

use Ibexa\Bundle\ProductCatalog\DependencyInjection\IbexaProductCatalogExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

final class IbexaProductCatalogExtensionTest extends AbstractExtensionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->container->setParameter('kernel.bundles', []);
    }

    protected function getContainerExtensions(): array
    {
        return [
            new IbexaProductCatalogExtension(),
        ];
    }

    public function testConfiguration(): void
    {
        $this->load([
            'engines' => [
                'foo' => [
                    'type' => 'local',
                    'options' => [
                        'foo' => 'foo',
                        'bar' => 'bar',
                        'baz' => 'baz',
                    ],
                ],
                'bar' => [
                    'type' => 'in_memory',
                ],
            ],
        ]);

        self::assertEquals([
            'foo' => [
                'type' => 'local',
                'options' => [
                    'foo' => 'foo',
                    'bar' => 'bar',
                    'baz' => 'baz',
                ],
            ],
            'bar' => [
                'type' => 'in_memory',
                'options' => [],
            ],
        ], $this->container->getParameter('ibexa.product_catalog.engines'));
    }
}
