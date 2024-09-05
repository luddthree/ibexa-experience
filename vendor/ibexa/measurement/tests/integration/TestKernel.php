<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Measurement;

use DAMA\DoctrineTestBundle\DAMADoctrineTestBundle;
use Hautelook\TemplatedUriBundle\HautelookTemplatedUriBundle;
use Ibexa\AdminUi\EventListener\AdminExceptionListener;
use Ibexa\AdminUi\Menu\MenuItemFactory;
use Ibexa\Bundle\AdminUi\IbexaAdminUiBundle;
use Ibexa\Bundle\Commerce\Eshop\Api\Configuration\CommerceSiteConfig;
use Ibexa\Bundle\ContentForms\IbexaContentFormsBundle;
use Ibexa\Bundle\CorePersistence\IbexaCorePersistenceBundle;
use Ibexa\Bundle\DesignEngine\IbexaDesignEngineBundle;
use Ibexa\Bundle\Elasticsearch\IbexaElasticsearchBundle;
use Ibexa\Bundle\FieldTypeRichText\IbexaFieldTypeRichTextBundle;
use Ibexa\Bundle\Measurement\IbexaMeasurementBundle;
use Ibexa\Bundle\Migration\IbexaMigrationBundle;
use Ibexa\Bundle\Notifications\IbexaNotificationsBundle;
use Ibexa\Bundle\ProductCatalog\IbexaProductCatalogBundle;
use Ibexa\Bundle\Rest\IbexaRestBundle;
use Ibexa\Bundle\Search\IbexaSearchBundle;
use Ibexa\Bundle\Solr\IbexaSolrBundle;
use Ibexa\Bundle\Taxonomy\IbexaTaxonomyBundle;
use Ibexa\Bundle\User\Controller\PasswordResetController;
use Ibexa\Bundle\User\IbexaUserBundle;
use Ibexa\Contracts\Measurement\MeasurementServiceInterface;
use Ibexa\Contracts\Migration\Metadata\Storage\MetadataStorage;
use Ibexa\Contracts\Migration\MigrationService;
use Ibexa\Contracts\Migration\MigrationStorage;
use Ibexa\Contracts\ProductCatalog\AttributeTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\LocalAssetServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\LocalAttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\LocalAttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\LocalProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\LocalProductTypeServiceInterface;
use Ibexa\Contracts\Test\Core\IbexaTestKernel;
use Ibexa\GraphQL\Schema\Domain\BaseNameHelper;
use Ibexa\Measurement\ProductCatalog\Form\Attribute\MeasurementOptionsValidator;
use Ibexa\Measurement\ProductCatalog\Form\Attribute\MeasurementRangeOptionsValidator;
use Ibexa\Measurement\ProductCatalog\Form\Attribute\MeasurementSimpleOptionsValidator;
use Ibexa\Measurement\UnitConverter\UnitConverterDispatcher;
use Ibexa\Migration\Metadata\Storage\InMemoryMetadataStorage;
use Ibexa\Migration\Storage\InMemoryMigrationStorage;
use Ibexa\Personalization\Authentication\AuthenticationInterface;
use Ibexa\Personalization\Service\Storage\DataSourceServiceInterface;
use Ibexa\ProductCatalog\GraphQL\Schema\NameHelper;
use Ibexa\Rest\Server\Controller\JWT;
use Ibexa\Solr\Handler as SolrHandler;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @internal
 */
final class TestKernel extends IbexaTestKernel
{
    public function registerBundles(): iterable
    {
        yield from parent::registerBundles();

        yield new IbexaCorePersistenceBundle();
        yield new IbexaProductCatalogBundle();
        yield new IbexaMigrationBundle();
        yield new IbexaNotificationsBundle();

        yield new IbexaAdminUiBundle();
        yield new IbexaDesignEngineBundle();
        yield new IbexaContentFormsBundle();
        yield new IbexaUserBundle();
        yield new IbexaSearchBundle();
        yield new IbexaFieldTypeRichTextBundle();
        yield new IbexaTaxonomyBundle();
        yield new IbexaRestBundle();
        yield new HautelookTemplatedUriBundle();

        yield new DAMADoctrineTestBundle();

        yield new IbexaSolrBundle();
        yield new IbexaElasticsearchBundle();

        yield new IbexaMeasurementBundle();
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        parent::registerContainerConfiguration($loader);

        $loader->load(__DIR__ . '/Resources/ibexa.yaml');
        $loader->load(__DIR__ . '/Resources/ibexa_measurement.yaml');
        $loader->load(__DIR__ . '/Resources/ibexa_product_catalog.yaml');

        $loader->load(__DIR__ . '/Resources/ibexa_solr.yaml');
        $loader->load(__DIR__ . '/Resources/ibexa_elasticsearch.yaml');

        $loader->load(static function (ContainerBuilder $container): void {
            $container->setParameter('form.type_extension.csrf.enabled', false);
            $container->setParameter('locale_fallback', 'en');

            self::addSyntheticService($container, AdminExceptionListener::class);
            self::addSyntheticService($container, JWT::class);
            self::addSyntheticService($container, MenuItemFactory::class);
            self::addSyntheticService($container, PasswordResetController::class);
            self::addSyntheticService($container, \Swift_Mailer::class);
            self::addSyntheticService($container, NameHelper::class);
            self::addSyntheticService($container, CommerceSiteConfig::class);

            self::addSyntheticService($container, BaseNameHelper::class);

            $container->loadFromExtension('framework', [
                'router' => [
                    'resource' => __DIR__ . '/Resources/routing.yaml',
                ],
            ]);

            $container->setDefinition(MetadataStorage::class, new Definition(InMemoryMetadataStorage::class));
            $container->setDefinition(MigrationStorage::class, new Definition(InMemoryMigrationStorage::class));

            self::createSyntheticPersonalizationServices($container);
        });

        $loader->load(dirname(__DIR__, 2) . '/src/bundle/Resources/config/services.yaml');
    }

    protected static function getExposedServicesByClass(): iterable
    {
        yield from parent::getExposedServicesByClass();

        yield MigrationService::class;
        yield UnitConverterDispatcher::class;

        yield MeasurementServiceInterface::class;
        yield MeasurementOptionsValidator::class;
        yield MeasurementSimpleOptionsValidator::class;
        yield MeasurementRangeOptionsValidator::class;

        yield LocalProductTypeServiceInterface::class;
        yield LocalProductServiceInterface::class;
        yield AttributeTypeServiceInterface::class;
        yield LocalAttributeGroupServiceInterface::class;
        yield LocalAttributeDefinitionServiceInterface::class;
        yield LocalAssetServiceInterface::class;

        yield SolrHandler::class;
    }

    public function getSchemaFiles(): iterable
    {
        yield from parent::getSchemaFiles();

        yield from [
            $this->locateResource('@IbexaProductCatalogBundle/Resources/config/schema.yaml'),
            $this->locateResource('@IbexaMeasurementBundle/Resources/config/schema.yaml'),
        ];
    }

    private static function createSyntheticPersonalizationServices(ContainerBuilder $container): void
    {
        self::addSyntheticService($container, AuthenticationInterface::class);
        self::addSyntheticService($container, DataSourceServiceInterface::class);
    }
}
