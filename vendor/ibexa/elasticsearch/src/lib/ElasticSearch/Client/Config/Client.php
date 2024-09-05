<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\ElasticSearch\Client\Config;

use Ibexa\Core\Base\Exceptions\InvalidArgumentException;

final class Client
{
    /** @var \Ibexa\Elasticsearch\ElasticSearch\Client\Config\Host[] */
    private $hosts;

    /** @var string|null */
    private $elasticCloudId;

    /** @var \Ibexa\Elasticsearch\ElasticSearch\Client\Config\Authentication|null */
    private $authentication;

    /** @var \Ibexa\Elasticsearch\ElasticSearch\Client\Config\SSL|null */
    private $ssl;

    /** @var string|null */
    private $connectionPool;

    /** @var string|null */
    private $connectionSelector;

    /** @var int|null */
    private $retries;

    /** @var string[] */
    private $indexTemplates;

    /** @var bool */
    private $debug;

    /** @var bool */
    private $trace;

    /**
     * @param \Ibexa\Elasticsearch\ElasticSearch\Client\Config\Host[] $hosts
     */
    public function __construct(
        array $hosts = [],
        ?string $elasticCloudId = null,
        ?Authentication $authentication = null,
        ?SSL $ssl = null,
        ?string $connectionPool = null,
        ?string $connectionSelector = null,
        ?int $retries = null,
        array $indexTemplates = [],
        bool $debug = false,
        bool $trace = false
    ) {
        $this->hosts = $hosts;
        $this->elasticCloudId = $elasticCloudId;
        $this->authentication = $authentication;
        $this->ssl = $ssl;
        $this->connectionPool = $connectionPool;
        $this->connectionSelector = $connectionSelector;
        $this->retries = $retries;
        $this->indexTemplates = $indexTemplates;
        $this->debug = $debug;
        $this->trace = $trace;
    }

    public function getHosts(): array
    {
        return $this->hosts;
    }

    public function getElasticCloudId(): ?string
    {
        return $this->elasticCloudId;
    }

    public function getAuthentication(): ?Authentication
    {
        return $this->authentication;
    }

    public function getSSL(): ?SSL
    {
        return $this->ssl;
    }

    public function getIndexTemplates(): array
    {
        return $this->indexTemplates;
    }

    public function getConnectionPool(): ?string
    {
        return $this->connectionPool;
    }

    public function getConnectionSelector(): ?string
    {
        return $this->connectionSelector;
    }

    public function getRetries(): ?int
    {
        return $this->retries;
    }

    public function isDebug(): bool
    {
        return $this->debug;
    }

    public function isTrace(): bool
    {
        return $this->trace;
    }

    public static function fromArray(array $properties): self
    {
        $hosts = [];
        if (is_array($properties['hosts'] ?? null)) {
            foreach ($properties['hosts'] as $i => $host) {
                if (!empty($host['dsn'])) {
                    $hosts[] = Host::fromString($host['dsn']);
                } elseif (is_array($host)) {
                    $hosts[] = Host::fromArray($host);
                } else {
                    throw new InvalidArgumentException(sprintf('$properties["hosts"][%d]', $i), 'Invalid host');
                }
            }
        }

        $auth = null;
        if (is_array($properties['authentication'] ?? null)) {
            $auth = Authentication::fromArray($properties['authentication']);
        }

        $ssl = null;
        if (is_array($properties['ssl'] ?? null)) {
            $ssl = SSL::fromArray($properties['ssl']);
        }

        return new self(
            $hosts,
            $properties['elastic_cloud_id'] ?? null,
            $auth,
            $ssl,
            $properties['connection_pool'] ?? null,
            $properties['connection_selector'] ?? null,
            $properties['retries'] ?? null,
            $properties['index_templates'] ?? [],
            $properties['debug'] ?? false,
            $properties['trace'] ?? false
        );
    }
}

class_alias(Client::class, 'Ibexa\Platform\ElasticSearchEngine\ElasticSearch\Client\Config\Client');
