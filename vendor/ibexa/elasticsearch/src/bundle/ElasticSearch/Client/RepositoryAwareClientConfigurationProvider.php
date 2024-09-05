<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Elasticsearch\ElasticSearch\Client;

use Ibexa\Bundle\Core\ApiLoader\RepositoryConfigurationProvider;
use Ibexa\Elasticsearch\ElasticSearch\Client\ClientConfigurationProviderInterface;
use Ibexa\Elasticsearch\ElasticSearch\Client\Config\Client as ClientConfig;

final class RepositoryAwareClientConfigurationProvider implements ClientConfigurationProviderInterface
{
    /** @var \Ibexa\Elasticsearch\ElasticSearch\Client\ClientConfigurationProviderInterface */
    private $innerConfigurationProvider;

    /** @var \Ibexa\Bundle\Core\ApiLoader\RepositoryConfigurationProvider */
    private $repositoryConfigurationProvider;

    public function __construct(
        ClientConfigurationProviderInterface $innerConfigurationProvider,
        RepositoryConfigurationProvider $repositoryConfigurationProvider
    ) {
        $this->innerConfigurationProvider = $innerConfigurationProvider;
        $this->repositoryConfigurationProvider = $repositoryConfigurationProvider;
    }

    public function getClientConfiguration(?string $name = null): ClientConfig
    {
        if ($name === null) {
            $name = $this->resolveDefaultConfigurationName();
        }

        return $this->innerConfigurationProvider->getClientConfiguration($name);
    }

    private function resolveDefaultConfigurationName(): ?string
    {
        $config = $this->repositoryConfigurationProvider->getRepositoryConfig();

        return $config['search']['connection'] ?? null;
    }
}

class_alias(RepositoryAwareClientConfigurationProvider::class, 'Ibexa\Platform\Bundle\ElasticSearchEngine\ElasticSearch\Client\RepositoryAwareClientConfigurationProvider');
