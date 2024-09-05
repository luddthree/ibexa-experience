<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ActivityLog\DependencyInjection\Configuration\SiteAccessAware;

use Exception;
use Ibexa\Bundle\ActivityLog\DependencyInjection\Configuration\SiteAccessAware\ActivityLogParser;
use Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension;
use Ibexa\Core\MVC\Exception\ParameterNotFoundException;
use Ibexa\Tests\Bundle\Core\DependencyInjection\Configuration\Parser\AbstractParserTestCase;

/**
 * @covers \Ibexa\Bundle\ActivityLog\DependencyInjection\Configuration\SiteAccessAware\ActivityLogParser
 */
final class ActivityLogParserTest extends AbstractParserTestCase
{
    protected function getContainerExtensions(): array
    {
        return [
            new IbexaCoreExtension([
                new ActivityLogParser(),
            ]),
        ];
    }

    /**
     * @dataProvider dataProviderForTestSettings
     *
     * @param array<string,mixed> $config
     * @param array<string,mixed> $expected
     * @param array<string> $expectedNotSet
     */
    public function testSettings(array $config, array $expected, array $expectedNotSet = []): void
    {
        $this->load([
            'system' => [
                'ibexa_demo_site' => $config,
            ],
        ]);

        foreach ($expected as $key => $val) {
            $this->assertConfigResolverParameterValue($key, $val, 'ibexa_demo_site');
        }

        foreach ($expectedNotSet as $key) {
            $this->assertConfigResolverParameterIsNotSet($key, 'ibexa_demo_site');
        }
    }

    /**
     * @phpstan-return iterable<
     *     string,
     *     array{
     *         array<string, mixed>,
     *         array<string, mixed>,
     *         2?: array<string>,
     *     },
     * >
     */
    public function dataProviderForTestSettings(): iterable
    {
        yield 'empty configuration' => [
            [],
            [],
            [
                'activity_log.pagination.activity_logs_limit',
            ],
        ];

        yield 'pagination' => [
            [
                'activity_log' => [
                    'pagination' => [
                        'activity_logs_limit' => 10,
                    ],
                ],
            ],
            [
                'activity_log.pagination.activity_logs_limit' => 10,
            ],
        ];
    }

    private function assertConfigResolverParameterIsNotSet(string $parameterName, ?string $scope = null): void
    {
        $chainConfigResolver = $this->getConfigResolver();
        try {
            $chainConfigResolver->getParameter($parameterName, 'ibexa.site_access.config', $scope);
            self::fail(sprintf('Parameter "%s" should not exist in scope "%s"', $parameterName, $scope));
        } catch (Exception $e) {
            self::assertInstanceOf(ParameterNotFoundException::class, $e);
        }
    }
}
