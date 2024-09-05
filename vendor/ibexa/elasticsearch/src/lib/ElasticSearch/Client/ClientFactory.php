<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\ElasticSearch\Client;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Ibexa\Elasticsearch\ElasticSearch\Client\Config\Authentication;
use Ibexa\Elasticsearch\ElasticSearch\Client\Config\Host;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use RuntimeException;

final class ClientFactory implements LoggerAwareInterface, ClientFactoryInterface
{
    use LoggerAwareTrait;

    /** @var \Ibexa\Elasticsearch\ElasticSearch\Client\ClientConfigurationProviderInterface */
    private $clientConfigurationProvider;

    public function __construct(ClientConfigurationProviderInterface $clientConfigurationProvider)
    {
        $this->logger = new NullLogger();
        $this->clientConfigurationProvider = $clientConfigurationProvider;
    }

    public function create(?string $name = null): Client
    {
        $builder = new ClientBuilder();
        $config = $this->clientConfigurationProvider->getClientConfiguration($name);

        $this->configureHosts($config, $builder);

        if ($config->getElasticCloudId() !== null) {
            $builder->setElasticCloudId($config->getElasticCloudId());
        }

        $this->configureAuthentication($config, $builder);
        $this->configureSSL($config, $builder);

        if ($config->getConnectionPool() !== null) {
            $builder->setConnectionPool($config->getConnectionPool());
        }

        if ($config->getConnectionSelector() !== null) {
            $builder->setSelector($config->getConnectionSelector());
        }

        if ($config->getRetries() !== null) {
            $builder->setRetries($config->getRetries());
        }

        if ($config->isDebug()) {
            $builder->setLogger($this->logger);
        }

        if ($config->isTrace()) {
            $builder->setTracer($this->logger);
        }

        return $builder->build();
    }

    private function configureHosts(Config\Client $config, ClientBuilder $builder): void
    {
        if (!empty($config->getHosts())) {
            $builder->setHosts(array_map(static function (Host $host): array {
                return [
                    'host' => $host->getHost(),
                    'port' => $host->getPort(),
                    'scheme' => $host->getScheme(),
                    'path' => $host->getPath(),
                    'user' => $host->getUser(),
                    'pass' => $host->getPass(),
                ];
            }, $config->getHosts()));
        }
    }

    private function configureAuthentication(Config\Client $config, ClientBuilder $builder): void
    {
        if ($config->getAuthentication()) {
            $authentication = $config->getAuthentication();

            switch ($authentication->getType()) {
                case Authentication::TYPE_BASIC:
                    $builder->setBasicAuthentication(...$authentication->getCredentials());
                    break;
                case Authentication::TYPE_API_KEY:
                    $builder->setApiKey(...$authentication->getCredentials());
                    break;
                default:
                    new RuntimeException('Unsupported authentication type: ' . $authentication->getType());
            }
        }
    }

    private function configureSSL(Config\Client $config, ClientBuilder $builder): void
    {
        if ($config->getSSL()) {
            $ssl = $config->getSSL();

            $sslVerification = $ssl->isVerificationEnabled() && $ssl->getSSLCACert()
                ? $ssl->getSSLCACert()->getPath()
                : $ssl->isVerificationEnabled();
            $builder->setSSLVerification($sslVerification);

            if ($ssl->getSSLCert()) {
                $builder->setSSLCert($ssl->getSSLCert()->getPath(), $ssl->getSSLCert()->getPass());
            }

            if ($ssl->getSSLKey()) {
                $builder->setSSLKey($ssl->getSSLKey()->getPath(), $ssl->getSSLKey()->getPass());
            }
        }
    }
}

class_alias(ClientFactory::class, 'Ibexa\Platform\ElasticSearchEngine\ElasticSearch\Client\ClientFactory');
