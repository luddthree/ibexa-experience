<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Taxonomy\DependencyInjection\Configuration\Parser;

use Exception;
use Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension;
use Ibexa\Bundle\Taxonomy\DependencyInjection\Configuration\Parser\Taxonomy;
use Ibexa\Core\MVC\Exception\ParameterNotFoundException;
use Ibexa\Tests\Bundle\Core\DependencyInjection\Configuration\Parser\AbstractParserTestCase;

/**
 * @covers \Ibexa\Bundle\Taxonomy\DependencyInjection\Configuration\Parser\Taxonomy
 */
final class TaxonomyTest extends AbstractParserTestCase
{
    protected function getContainerExtensions(): array
    {
        return [
            new IbexaCoreExtension([
                new Taxonomy(),
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
                'tab_assigned_content_limit',
            ],
        ];

        yield 'pagination' => [
            [
                'taxonomy' => [
                    'pagination' => [
                        'tab_assigned_content_limit' => 10,
                    ],
                ],
            ],
            [
                'taxonomy.pagination.tab_assigned_content_limit' => 10,
            ],
            [],
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
