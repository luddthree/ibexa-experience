<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Config;

use Ibexa\Bundle\Core\ApiLoader\RepositoryConfigurationProvider;
use Ibexa\Contracts\ProductCatalog\Exceptions\ConfigurationException;

final class ConfigProvider implements ConfigProviderInterface
{
    public const PRODUCT_CATALOG_ROOT = 'product_catalog';

    private RepositoryConfigurationProvider $repositoryConfigProvider;

    /** @var array<string, array{type: string, options: array}> */
    private array $engines;

    /**
     * @phpstan-param array<string, array{type: string, options: array}> $engines
     */
    public function __construct(
        RepositoryConfigurationProvider $repositoryConfigurationProvider,
        array $engines
    ) {
        $this->repositoryConfigProvider = $repositoryConfigurationProvider;
        $this->engines = $engines;
    }

    public function getEngineAlias(): ?string
    {
        $repositoryConfig = $this->repositoryConfigProvider->getRepositoryConfig();

        return $repositoryConfig['product_catalog']['engine'] ?? null;
    }

    public function getEngineType(): string
    {
        return $this->engines[$this->getEngineAliasOrThrow()]['type'];
    }

    public function getEngineOption(string $name, $default = null)
    {
        $engine = $this->getEngineAliasOrThrow();

        return $this->engines[$engine]['options'][$name] ?? $default;
    }

    /**
     * @throws \Ibexa\Contracts\ProductCatalog\Exceptions\ConfigurationException
     */
    private function getEngineAliasOrThrow(): string
    {
        $engine = $this->getEngineAlias();
        if ($engine === null) {
            throw new ConfigurationException('Missing product catalog engine configuration');
        }

        if (!isset($this->engines[$engine])) {
            throw new ConfigurationException(sprintf(
                'Invalid product catalog engine alias: "%s". Expected one of: "%s"',
                $engine,
                implode('", "', array_keys($this->engines)),
            ));
        }

        return $engine;
    }
}
