<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Elasticsearch;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Ibexa\Contracts\Core\Persistence\Content\ContentInfo;
use Ibexa\Contracts\Core\Test\Repository\SetupFactory\Legacy as CoreLegacySetupFactory;
use Ibexa\Core\Base\ServiceContainer;
use Ibexa\Core\Persistence\Legacy\Content\Gateway as ContentGateway;
use Ibexa\Elasticsearch\ElasticSearch\Client\ClientFactoryInterface;
use Ibexa\Elasticsearch\Indexer;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;

final class LegacySetupFactory extends CoreLegacySetupFactory
{
    use CoreSetupFactoryTrait;

    public function getRepository($initializeFromScratch = true)
    {
        // Load repository first so all initialization steps are done
        $repository = parent::getRepository($initializeFromScratch);

        if ($initializeFromScratch) {
            $this->indexAll();
        }

        return $repository;
    }

    protected function externalBuildContainer(ContainerBuilder $containerBuilder)
    {
        $this->loadCoreSettings($containerBuilder);

        $settingsPath = __DIR__ . '/../../src/bundle/Resources/config/';

        $loader = new YamlFileLoader($containerBuilder, new FileLocator($settingsPath));
        $loader->load('services.yaml');
        $loader->load('services-test.yaml');
    }

    private function indexAll(): void
    {
        $serviceContainer = $this->getServiceContainer();

        $this->initializeElasticSearch($serviceContainer);
        /** @var \Ibexa\Elasticsearch\Indexer $indexer */
        $indexer = $serviceContainer->get(Indexer::class);
        $indexer->purge();
        $indexer->updateSearchIndex(
            $this->getContentIdsToIndex($serviceContainer),
            true
        );
    }

    private function getContentIdsToIndex(ServiceContainer $serviceContainer): array
    {
        $connection = $this->getDatabaseConnection($serviceContainer);

        $query = $connection->createQueryBuilder();
        $query
            ->select('id')
            ->from(ContentGateway::CONTENT_ITEM_TABLE)
            ->where($query->expr()->eq('status', ContentInfo::STATUS_PUBLISHED));

        return array_map(
            'intval',
            $query->execute()->fetchAll(FetchMode::COLUMN)
        );
    }

    private function initializeElasticSearch(ServiceContainer $serviceContainer): void
    {
        $schema = Yaml::parseFile(__DIR__ . '/Resources/index-template.yaml');

        /** @var \Elasticsearch\Client $client */
        $client = $serviceContainer->get(ClientFactoryInterface::class)->create();
        $client->indices()->putTemplate([
            'name' => 'ezplatform_integration_tests',
            'body' => $schema,
        ]);
    }

    private function getDatabaseConnection(ServiceContainer $serviceContainer): Connection
    {
        /** @var \Doctrine\DBAL\Connection $connection */
        $connection = $serviceContainer->get(
            'ibexa.persistence.connection'
        );

        return $connection;
    }
}
