<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Dashboard\DependencyInjection\Parser;

use Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension;
use Ibexa\Bundle\Dashboard\DependencyInjection\Parser\Dashboard;
use Ibexa\Tests\Bundle\Core\DependencyInjection\Configuration\Parser\AbstractParserTestCase;

final class DashboardParserTest extends AbstractParserTestCase
{
    protected function getContainerExtensions(): array
    {
        return [
            new IbexaCoreExtension([new Dashboard()]),
        ];
    }

    /**
     * @dataProvider dataProviderForSettings
     *
     * @param array<string,mixed> $config
     * @param array<string,mixed> $expected
     */
    public function testSettings(array $config, array $expected): void
    {
        $this->load([
            'system' => [
                'ibexa_demo_site' => $config,
            ],
        ]);

        foreach ($expected as $key => $val) {
            $this->assertConfigResolverParameterValue($key, $val, 'ibexa_demo_site');
        }
    }

    /**
     * @phpstan-return iterable<
     *      int,
     *      array{
     *          array<string,mixed>,
     *          array<string,mixed>,
     *      }
     *  >
     */
    public function dataProviderForSettings(): iterable
    {
        return [
            [
                [
                    'dashboard' => null,
                ],
                [
                    'dashboard.container_remote_id' => 'dashboard_container',
                    'dashboard.default_dashboard_remote_id' => 'default_dashboard',
                    'dashboard.users_container_remote_id' => 'user_dashboards',
                    'dashboard.predefined_container_remote_id' => 'predefined_dashboards',
                    'dashboard.section_identifier' => 'dashboard',
                    'dashboard.content_type_identifier' => 'dashboard_landing_page',
                    'dashboard.container_content_type_identifier' => 'folder',
                ],
            ],
            [
                [
                    'dashboard' => [
                        'container_remote_id' => 'remote_id',
                        'default_dashboard_remote_id' => 'remote_id_bar',
                        'users_container_remote_id' => 'remote_id_baz',
                        'predefined_container_remote_id' => 'remote_id_pre',
                        'section_identifier' => 'section_identifier_foo',
                        'content_type_identifier' => 'content_type_identifier_foo',
                        'container_content_type_identifier' => 'folder_foo',
                    ],
                ],
                [
                    'dashboard.container_remote_id' => 'remote_id',
                    'dashboard.default_dashboard_remote_id' => 'remote_id_bar',
                    'dashboard.users_container_remote_id' => 'remote_id_baz',
                    'dashboard.predefined_container_remote_id' => 'remote_id_pre',
                    'dashboard.section_identifier' => 'section_identifier_foo',
                    'dashboard.content_type_identifier' => 'content_type_identifier_foo',
                    'dashboard.container_content_type_identifier' => 'folder_foo',
                ],
            ],
        ];
    }
}
