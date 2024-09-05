<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Taxonomy;

use DAMA\DoctrineTestBundle\DAMADoctrineTestBundle;
use Hautelook\TemplatedUriBundle\HautelookTemplatedUriBundle;
use Ibexa\Bundle\AdminUi\IbexaAdminUiBundle;
use Ibexa\Bundle\ContentForms\IbexaContentFormsBundle;
use Ibexa\Bundle\Elasticsearch\IbexaElasticsearchBundle;
use Ibexa\Bundle\FieldTypeRichText\IbexaFieldTypeRichTextBundle;
use Ibexa\Bundle\Migration\IbexaMigrationBundle;
use Ibexa\Bundle\Notifications\IbexaNotificationsBundle;
use Ibexa\Bundle\Rest\IbexaRestBundle;
use Ibexa\Bundle\Search\IbexaSearchBundle;
use Ibexa\Bundle\Solr\IbexaSolrBundle;
use Ibexa\Bundle\Taxonomy\IbexaTaxonomyBundle;
use Ibexa\Bundle\User\IbexaUserBundle;
use Ibexa\Contracts\Core\Persistence\Content\Language\Handler as LanguageHandler;
use Ibexa\Contracts\Core\Repository\LanguageResolver;
use Ibexa\Contracts\Core\Search\Handler;
use Ibexa\Contracts\Migration\Metadata\Storage\MetadataStorage;
use Ibexa\Contracts\Migration\MigrationStorage;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Contracts\Test\Core\IbexaTestKernel as BaseIbexaTestKernel;
use Ibexa\Core\Search\Common\Indexer;
use Ibexa\Elasticsearch\ElasticSearch\Client\ClientFactoryInterface;
use Ibexa\GraphQL\Schema\Domain\BaseNameHelper;
use Ibexa\Migration\Metadata\Storage\InMemoryMetadataStorage;
use Ibexa\Migration\Reference\CollectorInterface;
use Ibexa\Migration\Storage\InMemoryMigrationStorage;
use Ibexa\Rest\Server\Controller\JWT;
use Ibexa\Solr\Handler as SolrHandler;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;
use Ibexa\Tests\Integration\Taxonomy\DependencyInjection\Configuration\SiteAccessAware\IgnoredConfigParser;
use Knp\Bundle\MenuBundle\KnpMenuBundle;
use Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle;
use Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\WebpackEncoreBundle\WebpackEncoreBundle;

final class IbexaTestKernel extends BaseIbexaTestKernel
{
    public function registerBundles(): iterable
    {
        yield from parent::registerBundles();

        yield from [
            new IbexaSolrBundle(),
            new IbexaAdminUiBundle(),
            new IbexaContentFormsBundle(),
            new IbexaSearchBundle(),
            new IbexaUserBundle(),
            new IbexaNotificationsBundle(),
            new KnpMenuBundle(),
            new IbexaElasticsearchBundle(),
            new IbexaMigrationBundle(),
            new IbexaRestBundle(),
            new IbexaFieldTypeRichTextBundle(),
            new IbexaTaxonomyBundle(),
            new DAMADoctrineTestBundle(),
            new StofDoctrineExtensionsBundle(),
            new WebpackEncoreBundle(),
            new SwiftmailerBundle(),
            new HautelookTemplatedUriBundle(),
        ];
    }

    protected static function getExposedServicesByClass(): iterable
    {
        yield from parent::getExposedServicesByClass();

        yield TaxonomyServiceInterface::class;
        yield TaxonomyConfiguration::class;
        yield LanguageResolver::class;
        yield ClientFactoryInterface::class;

        // Used in migrations
        yield CollectorInterface::class;
        yield LanguageHandler::class;

        yield Indexer::class;
    }

    protected static function getExposedServicesById(): iterable
    {
        yield from parent::getExposedServicesById();

        yield 'ibexa.spi.search' => Handler::class;
        yield SolrHandler::class => Handler::class;

        yield 'ibexa.migrations.serializer' => SerializerInterface::class;
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        parent::registerContainerConfiguration($loader);

        $loader->load(__DIR__ . '/Resources/framework.yaml');
        $loader->load(__DIR__ . '/Resources/ibexa.yaml');
        $loader->load(__DIR__ . '/Resources/ibexa_elasticsearch.yaml');
        $loader->load(__DIR__ . '/Resources/ibexa_taxonomy.yaml');
        $loader->load(__DIR__ . '/Resources/ibexa_solr.yaml');
        $loader->load(__DIR__ . '/Resources/liip_imagine.yaml');
        $loader->load(__DIR__ . '/Resources/stof_doctrine_extensions.yaml');
        $loader->load(__DIR__ . '/Resources/webpack_encore.yaml');

        $loader->load(static function (ContainerBuilder $container): void {
            $resource = new FileResource(__DIR__ . '/Resources/routing.yaml');
            $container->addResource($resource);
            $container->loadFromExtension('framework', [
                'router' => [
                    'resource' => $resource->getResource(),
                ],
            ]);
        });

        $loader->load(static function (ContainerBuilder $container): void {
            self::prepareIbexaFramework($container);
            self::prepareDatabaseConnection($container);
            self::createSyntheticAdminUiServices($container);
        });
    }

    private static function prepareDatabaseConnection(ContainerBuilder $container): void
    {
        $container->setDefinition(MetadataStorage::class, new Definition(InMemoryMetadataStorage::class));
        $container->setDefinition(MigrationStorage::class, new Definition(InMemoryMigrationStorage::class));
    }

    private static function prepareIbexaFramework(ContainerBuilder $container): void
    {
        /** @var \Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension $kernel */
        $kernel = $container->getExtension('ibexa');
        $kernel->addConfigParser(new IgnoredConfigParser([
            'content_create_view',
            'content_translate_view',
            'content_edit_view',
            'admin_ui_forms',
            'limitation_value_templates',
            'design',
            'user_settings_update_view',
            'search_view',
        ]));
    }

    private static function createSyntheticAdminUiServices(ContainerBuilder $container): void
    {
        self::addSyntheticService($container, JWT::class);
        self::addSyntheticService($container, BaseNameHelper::class);
    }
}
