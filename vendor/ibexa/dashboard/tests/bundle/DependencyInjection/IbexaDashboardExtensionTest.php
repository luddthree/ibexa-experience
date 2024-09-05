<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Dashboard\DependencyInjection;

use Ibexa\Bundle\Dashboard\DependencyInjection\IbexaDashboardExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

/**
 * @phpstan-type TExpectedConfigArray array<string, mixed>
 */
final class IbexaDashboardExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions(): array
    {
        return [new IbexaDashboardExtension()];
    }

    /**
     * @phpstan-return iterable<string, array{string, TExpectedConfigArray}>
     */
    public function getDataForTestExtensionPrependsConfiguration(): iterable
    {
        yield 'ibexa' => [
            'ibexa',
            [
                'system' => [
                    'default' => [
                        'universal_discovery_widget_module' => [
                            'configuration' => [
                                'quick_action_create_form' => [
                                    'tabs' => [
                                        'search' => ['hidden' => true],
                                        'bookmarks' => ['hidden' => true],
                                    ],
                                    'multiple' => false,
                                    'allowed_content_types' => ['form', 'folder'],
                                    'content_on_the_fly' => ['preselected_content_type' => 'form'],
                                    'allow_confirmation' => false,
                                ],
                            ],
                        ],
                    ],
                    'admin_group' => [
                        'content_view' => [
                            'dashboard' => [
                                'dashboard_page' => [
                                    'template' => '@ibexadesign/dashboard/dashboard_view.html.twig',
                                    'match' => [
                                        'Ibexa\IsDashboard' => true,
                                        'IsPreview' => false,
                                    ],
                                ],
                                'dashboard_preview_page' => [
                                    'template' => '@ibexadesign/dashboard/dashboard_preview.html.twig',
                                    'match' => [
                                        'Ibexa\IsDashboard' => true,
                                        'IsPreview' => true,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        yield 'ibexa_fieldtype_page' => [
            'ibexa_fieldtype_page',
            [
                'layouts' => [
                    'dashboard_one_column' => [
                        'identifier' => 'dashboard_one_column',
                        'name' => 'One column',
                        'description' => 'One column',
                        'thumbnail' => '/bundles/ibexadashboard/img/layouts/one_column.svg',
                        'template' => '@ibexadesign/dashboard/builder/layouts/one_column.html.twig',
                        'zones' => [
                            'top' => [
                                'name' => 'top',
                            ],
                        ],
                    ],
                    'dashboard_two_columns' => [
                        'identifier' => 'dashboard_two_columns',
                        'name' => 'Two columns',
                        'description' => 'Two columns',
                        'thumbnail' => '/bundles/ibexadashboard/img/layouts/two_columns.svg',
                        'template' => '@ibexadesign/dashboard/builder/layouts/two_columns.html.twig',
                        'zones' => [
                            'left' => [
                                'name' => 'left',
                            ],
                            'right' => [
                                'name' => 'right',
                            ],
                        ],
                    ],
                    'dashboard_one_third_left' => [
                        'identifier' => 'dashboard_one_third_left',
                        'name' => 'One-third left',
                        'description' => 'One-third left',
                        'thumbnail' => '/bundles/ibexadashboard/img/layouts/one_third_left.svg',
                        'template' => '@ibexadesign/dashboard/builder/layouts/one_third_left.html.twig',
                        'zones' => [
                            'left' => [
                                'name' => 'left',
                            ],
                            'right' => [
                                'name' => 'right',
                            ],
                        ],
                    ],
                    'dashboard_one_third_right' => [
                        'identifier' => 'dashboard_one_third_right',
                        'name' => 'One-third right',
                        'description' => 'One-third right',
                        'thumbnail' => '/bundles/ibexadashboard/img/layouts/one_third_right.svg',
                        'template' => '@ibexadesign/dashboard/builder/layouts/one_third_right.html.twig',
                        'zones' => [
                            'left' => [
                                'name' => 'left',
                            ],
                            'right' => [
                                'name' => 'right',
                            ],
                        ],
                    ],
                    'dashboard_two_rows_two_columns' => [
                        'identifier' => 'dashboard_two_rows_two_columns',
                        'name' => 'Two rows, two columns',
                        'description' => 'Two rows, two columns',
                        'thumbnail' => '/bundles/ibexadashboard/img/layouts/two_rows_two_columns.svg',
                        'template' => '@ibexadesign/dashboard/builder/layouts/two_rows_two_columns.html.twig',
                        'zones' => [
                            'top' => [
                                'name' => 'top',
                            ],
                            'bottom_left' => [
                                'name' => 'bottom-left',
                            ],
                            'bottom_right' => [
                                'name' => 'bottom-right',
                            ],
                        ],
                    ],
                    'dashboard_three_rows_two_columns' => [
                        'identifier' => 'dashboard_three_rows_two_columns',
                        'name' => 'Three rows, two columns',
                        'description' => 'Three rows, two columns',
                        'thumbnail' => '/bundles/ibexadashboard/img/layouts/three_rows_two_columns.svg',
                        'template' => '@ibexadesign/dashboard/builder/layouts/three_rows_two_columns.html.twig',
                        'zones' => [
                            'top' => [
                                'name' => 'top',
                            ],
                            'middle_left' => [
                                'name' => 'middle-left',
                            ],
                            'middle_right' => [
                                'name' => 'middle-right',
                            ],
                            'bottom' => [
                                'name' => 'bottom',
                            ],
                        ],
                    ],
                    'dashboard_three_rows_two_columns_2' => [
                        'identifier' => 'dashboard_three_rows_two_columns_2',
                        'name' => 'Three rows, two columns',
                        'description' => 'Three rows, two columns',
                        'thumbnail' => '/bundles/ibexadashboard/img/layouts/three_rows_two_columns_2.svg',
                        'template' => '@ibexadesign/dashboard/builder/layouts/three_rows_two_columns_2.html.twig',
                        'zones' => [
                            'top_left' => [
                                'name' => 'top-left',
                            ],
                            'top_right' => [
                                'name' => 'top-right',
                            ],
                            'middle' => [
                                'name' => 'middle',
                            ],
                            'bottom_left' => [
                                'name' => 'bottom-left',
                            ],
                            'bottom_right' => [
                                'name' => 'bottom-right',
                            ],
                        ],
                    ],
                    'dashboard_three_columns' => [
                        'identifier' => 'dashboard_three_columns',
                        'name' => 'Three columns',
                        'description' => 'Three columns',
                        'thumbnail' => '/bundles/ibexadashboard/img/layouts/three_columns.svg',
                        'template' => '@ibexadesign/dashboard/builder/layouts/three_columns.html.twig',
                        'zones' => [
                            'left' => [
                                'name' => 'left',
                            ],
                            'center' => [
                                'name' => 'center',
                            ],
                            'right' => [
                                'name' => 'right',
                            ],
                        ],
                    ],
                    'dashboard_two_rows_three_columns' => [
                        'identifier' => 'dashboard_two_rows_three_columns',
                        'name' => 'Two rows, three columns',
                        'description' => 'Two rows, three columns',
                        'thumbnail' => '/bundles/ibexadashboard/img/layouts/two_rows_three_columns.svg',
                        'template' => '@ibexadesign/dashboard/builder/layouts/two_rows_three_columns.html.twig',
                        'zones' => [
                            'top' => [
                                'name' => 'top',
                            ],
                            'bottom_left' => [
                                'name' => 'bottom-left',
                            ],
                            'bottom_center' => [
                                'name' => 'bottom-center',
                            ],
                            'bottom_right' => [
                                'name' => 'bottom-right',
                            ],
                        ],
                    ],
                    'dashboard_three_rows_three_columns' => [
                        'identifier' => 'dashboard_three_rows_three_columns',
                        'name' => 'Three rows, three columns',
                        'description' => 'Three rows, three columns',
                        'thumbnail' => '/bundles/ibexadashboard/img/layouts/three_rows_three_columns.svg',
                        'template' => '@ibexadesign/dashboard/builder/layouts/three_rows_three_columns.html.twig',
                        'zones' => [
                            'top' => [
                                'name' => 'top',
                            ],
                            'middle_left' => [
                                'name' => 'middle-left',
                            ],
                            'middle_center' => [
                                'name' => 'middle-center',
                            ],
                            'middle_right' => [
                                'name' => 'middle-right',
                            ],
                            'bottom' => [
                                'name' => 'bottom',
                            ],
                        ],
                    ],
                    'dashboard_three_rows_three_columns_2' => [
                        'identifier' => 'dashboard_three_rows_three_columns_2',
                        'name' => 'Three rows, three columns',
                        'description' => 'Three rows, three columns',
                        'thumbnail' => '/bundles/ibexadashboard/img/layouts/three_rows_three_columns_2.svg',
                        'template' => '@ibexadesign/dashboard/builder/layouts/three_rows_three_columns_2.html.twig',
                        'zones' => [
                            'top_left' => [
                                'name' => 'top-left',
                            ],
                            'top_center' => [
                                'name' => 'top-center',
                            ],
                            'top_right' => [
                                'name' => 'top-right',
                            ],
                            'middle' => [
                                'name' => 'middle',
                            ],
                            'bottom_left' => [
                                'name' => 'bottom-left',
                            ],
                            'bottom_center' => [
                                'name' => 'bottom-center',
                            ],
                            'bottom_right' => [
                                'name' => 'bottom-right',
                            ],
                        ],
                    ],
                ],
                'blocks' => [
                    'ibexa_news' => [
                        'name' => 'Ibexa news',
                        'category' => 'Dashboard',
                        'thumbnail' => '/bundles/ibexaicons/img/all-icons.svg#news',
                        'views' => [
                            'default' => [
                                'name' => 'Default block layout',
                                'template' => '@ibexadesign/dashboard/blocks/ibexa_news.html.twig',
                            ],
                        ],
                        'attributes' => [
                            'limit' => [
                                'name' => 'Number of news',
                                'type' => 'integer',
                                'value' => '%ibexa.dashboard.ibexa_news.limit%',
                                'options' => [
                                    'help' => 'Min. 1, max. 10',
                                ],
                                'validators' => [
                                    'not_blank' => [
                                        'message' => 'News limit cannot be empty.',
                                    ],
                                    'less_than' => [
                                        'options' => [
                                            'value' => 11,
                                        ],
                                        'message' => 'The limit should be a maximum of 10.',
                                    ],
                                    'regexp' => [
                                        'options' => [
                                            'pattern' => '/^[0-9]+$/',
                                        ],
                                        'message' => 'News limit must be a positive integer.',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'quick_actions' => [
                        'name' => 'Quick actions',
                        'category' => 'Dashboard',
                        'thumbnail' => '/bundles/ibexaicons/img/all-icons.svg#flash',
                        'views' => [
                            'default' => [
                                'name' => 'Default block layout',
                                'template' => '@ibexadesign/dashboard/blocks/quick_actions.html.twig',
                            ],
                        ],
                        'attributes' => [
                            'actions' => [
                                'type' => 'select',
                                'name' => 'Actions',
                                'value' => 'create_content,create_form,create_product,create_catalog,create_company',
                                'options' => [
                                    'multiple' => true,
                                    'choices' => [
                                        'Create content' => 'create_content',
                                        'Create form' => 'create_form',
                                        'Create product' => 'create_product',
                                        'Create catalog' => 'create_catalog',
                                        'Create company' => 'create_company',
                                    ],
                                ],
                                'identifier' => 'actions',
                                'validators' => [
                                    'not_blank' => [
                                        'message' => 'Actions cannot be empty.',
                                    ],
                                ],
                                'category' => 'default',
                            ],
                        ],
                    ],
                    'my_content' => [
                        'name' => 'My content',
                        'category' => 'Dashboard',
                        'thumbnail' => '/bundles/ibexaicons/img/all-icons.svg#contentlist',
                        'views' => [
                            'default' => [
                                'template' => '@ibexadesign/dashboard/blocks/my_content.html.twig',
                            ],
                        ],
                    ],
                    'common_content' => [
                        'name' => 'Common content',
                        'category' => 'Dashboard',
                        'thumbnail' => '/bundles/ibexaicons/img/all-icons.svg#content-write',
                        'views' => [
                            'default' => [
                                'template' => '@ibexadesign/dashboard/blocks/common_content.html.twig',
                            ],
                        ],
                    ],
                    'review_queue' => [
                        'name' => 'Review queue',
                        'category' => 'Dashboard',
                        'thumbnail' => '/bundles/ibexaicons/img/all-icons.svg#review',
                        'views' => [
                            'default' => [
                                'template' => '@ibexadesign/dashboard/blocks/review_queue.html.twig',
                            ],
                        ],
                    ],
                ],
                'block_validators' => [
                    'less_than' => 'Symfony\\Component\\Validator\\Constraints\\LessThan',
                ],
            ],
        ];

        yield 'bazinga_js_translation' => [
            'bazinga_js_translation',
            [
                'active_domains' => ['ibexa_dashboard'],
            ],
        ];
    }

    /**
     * @dataProvider getDataForTestExtensionPrependsConfiguration
     *
     * @phpstan-param TExpectedConfigArray $expectedConfig
     */
    public function testExtensionPrependsConfiguration(string $extensionName, array $expectedConfig): void
    {
        $this->load();

        self::assertSame(
            $expectedConfig,
            array_merge_recursive(...$this->container->getExtensionConfig($extensionName))
        );
    }
}
