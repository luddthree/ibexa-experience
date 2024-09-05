<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Connector\Dam\DependencyInjection\Configuration;

use Ibexa\Bundle\Connector\Dam\DependencyInjection\Configuration\Parser\DamConfigurationParser;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\Parser\Content;
use Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension;
use Ibexa\Tests\Bundle\Core\DependencyInjection\Configuration\Parser\AbstractParserTestCase;

final class DamConfigurationParserTest extends AbstractParserTestCase
{
    protected function getContainerExtensions(): array
    {
        return [
            new IbexaCoreExtension(
                [
                    new Content(),
                    // Configuration parser under test
                    new DamConfigurationParser(),
                ]
            ),
        ];
    }

    public function testEmptyConfiguration(): void
    {
        $this->load($this->buildConfiguration([]));

        $this->assertConfigResolverParameterIsNotSet('content.dam', 'ibexa_demo_site');
    }

    public function testDamConfiguration(): void
    {
        $config = $this->buildConfiguration([
            'dam' => ['bar'],
        ]);
        $this->load($config);

        $this->assertConfigResolverParameterValue('content.dam', ['bar'], 'ibexa_demo_site');
    }

    public function testUnsetConfiguration(): void
    {
        $config = $this->buildConfiguration([
            'dam' => false,
        ]);
        $this->load($config);

        $this->assertConfigResolverParameterIsNotSet('content.dam', 'ibexa_demo_site');
    }

    /**
     * @param array<string, mixed> $config
     *
     * @return array<string, array<mixed>>
     */
    private function buildConfiguration(array $config): array
    {
        return [
            'system' => [
                'ibexa_demo_site' => [
                    'content' => $config,
                ],
            ],
        ];
    }

    private function assertConfigResolverParameterIsNotSet(string $parameterName, ?string $scope = null): void
    {
        $chainConfigResolver = $this->getConfigResolver();
        self::assertFalse(
            $chainConfigResolver->hasParameter($parameterName, 'ibexa.site_access.config', $scope),
            sprintf('Parameter "%s" should not exist in scope "%s"', $parameterName, $scope)
        );
    }
}
