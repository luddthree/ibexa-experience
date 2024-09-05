<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\SiteContext\DependencyInjection\Configuration;

use Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension;
use Ibexa\Bundle\SiteContext\DependencyInjection\Configuration\ConfigurationParser;
use Ibexa\Tests\Bundle\Core\DependencyInjection\Configuration\Parser\AbstractParserTestCase;

final class ConfigurationParserTest extends AbstractParserTestCase
{
    protected function getContainerExtensions(): array
    {
        return [
            new IbexaCoreExtension([
                new ConfigurationParser(),
            ]),
        ];
    }

    /**
     * @dataProvider dataProviderForTestSettings
     *
     * @param array<string, mixed> $config
     * @param array<string, mixed> $expected
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
     *     string,
     *     array{
     *         array<string, mixed>,
     *         array<string, mixed>,
     *     },
     * >
     */
    public function dataProviderForTestSettings(): iterable
    {
        yield 'empty configuration' => [
            [],
            [
                'site_context.excluded_paths' => [],
            ],
        ];

        yield 'typical configuration' => [
            [
                'site_context' => [
                    'excluded_paths' => [
                        '/1/2/43',
                        '/1/2/54/56',
                    ],
                ],
            ],
            [
                'site_context.excluded_paths' => [
                    '/1/2/43',
                    '/1/2/54/56',
                ],
            ],
        ];
    }
}
