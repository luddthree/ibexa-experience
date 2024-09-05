<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\ElasticSearch\Client;

use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Elasticsearch\ElasticSearch\Client\Config\Client as ClientConfig;

final class ClientConfigurationProvider implements ClientConfigurationProviderInterface
{
    /** @var \Ibexa\Elasticsearch\ElasticSearch\Client\Config\Client[] */
    private $availableConfigurations;

    /** @var string */
    private $defaultConfigurationName;

    public function __construct(array $availableConfigurations, string $defaultConfigurationName)
    {
        $this->availableConfigurations = [];
        foreach ($availableConfigurations as $name => $configuration) {
            $this->availableConfigurations[$name] = ClientConfig::fromArray($configuration);
        }

        $this->defaultConfigurationName = $defaultConfigurationName;
    }

    public function getClientConfiguration(?string $name = null): ClientConfig
    {
        $name = $name ?? $this->defaultConfigurationName;

        if (isset($this->availableConfigurations[$name])) {
            return $this->availableConfigurations[$name];
        }

        throw new InvalidArgumentException('name', 'Undefined configuration with name ' . $name);
    }
}

class_alias(ClientConfigurationProvider::class, 'Ibexa\Platform\ElasticSearchEngine\ElasticSearch\Client\ClientConfigurationProvider');
