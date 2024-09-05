<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Taxonomy\DependencyInjection;

use Ibexa\Bundle\Taxonomy\DependencyInjection\IbexaTaxonomyExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

/**
 * @covers \Ibexa\Bundle\Taxonomy\DependencyInjection\IbexaTaxonomyExtension
 */
final class IbexaTaxonomyExtensionTest extends AbstractExtensionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->container->setParameter('kernel.bundles', []);
    }

    protected function getContainerExtensions(): array
    {
        return [
            new IbexaTaxonomyExtension(),
        ];
    }

    public function testConfiguration(): void
    {
        $this->load([
            'taxonomies' => [
                'tags' => [
                    'parent_location_remote_id' => 'taxonomy_tags_folder',
                    'content_type' => 'tag',
                    'field_mappings' => [
                        'identifier' => 'identifier',
                        'parent' => 'parent',
                        'name' => 'name',
                    ],
                    'assigned_content_tab' => true,
                ],
                'product_categories' => [
                    'parent_location_remote_id' => 'taxonomy_product_categories_folder',
                    'content_type' => 'product_category',
                    'field_mappings' => [
                        'identifier' => 'identifier',
                        'parent' => 'parent',
                        'name' => 'name',
                    ],
                    'register_main_menu' => false,
                    'assigned_content_tab' => false,
                ],
            ],
        ]);

        self::assertEquals(
            [
                'tags' => [
                    'parent_location_remote_id' => 'taxonomy_tags_folder',
                    'content_type' => 'tag',
                    'field_mappings' => [
                        'identifier' => 'identifier',
                        'parent' => 'parent',
                        'name' => 'name',
                    ],
                    'register_main_menu' => true,
                    'assigned_content_tab' => true,
                ],
                'product_categories' => [
                    'parent_location_remote_id' => 'taxonomy_product_categories_folder',
                    'content_type' => 'product_category',
                    'field_mappings' => [
                        'identifier' => 'identifier',
                        'parent' => 'parent',
                        'name' => 'name',
                    ],
                    'register_main_menu' => false,
                    'assigned_content_tab' => false,
                ],
            ],
            $this->container->getParameter('ibexa.taxonomy.taxonomies')
        );
    }
}
