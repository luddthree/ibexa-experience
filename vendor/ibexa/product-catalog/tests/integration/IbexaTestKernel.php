<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog;

use DAMA\DoctrineTestBundle\DAMADoctrineTestBundle;
use Hautelook\TemplatedUriBundle\HautelookTemplatedUriBundle;
use Ibexa\AdminUi\EventListener\AdminExceptionListener;
use Ibexa\AdminUi\Form\Data\FormMapper\ContentTypeDraftMapper;
use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\AdminUi\Menu\MenuItemFactory;
use Ibexa\AdminUi\UI\Config\Provider\ContentTypeMappings;
use Ibexa\AdminUi\UI\Module\FieldTypeToolbar\FieldTypeToolbarFactory;
use Ibexa\Bundle\AdminUi\Controller\ContentViewController;
use Ibexa\Bundle\AdminUi\IbexaAdminUiBundle;
use Ibexa\Bundle\Commerce\Checkout\Entity\BasketRepository;
use Ibexa\Bundle\Commerce\Eshop\Api\Configuration\CommerceSiteConfig;
use Ibexa\Bundle\Commerce\Eshop\Services\Url\BaseCatalogUrl;
use Ibexa\Bundle\ContentForms\IbexaContentFormsBundle;
use Ibexa\Bundle\CorePersistence\IbexaCorePersistenceBundle;
use Ibexa\Bundle\Elasticsearch\IbexaElasticsearchBundle;
use Ibexa\Bundle\FieldTypeMatrix\IbexaFieldTypeMatrixBundle;
use Ibexa\Bundle\FieldTypeRichText\IbexaFieldTypeRichTextBundle;
use Ibexa\Bundle\GraphQL\IbexaGraphQLBundle;
use Ibexa\Bundle\Migration\IbexaMigrationBundle;
use Ibexa\Bundle\Notifications\IbexaNotificationsBundle;
use Ibexa\Bundle\Personalization\Controller\RecommendationController;
use Ibexa\Bundle\Personalization\IbexaPersonalizationBundle;
use Ibexa\Bundle\ProductCatalog\IbexaProductCatalogBundle;
use Ibexa\Bundle\Rest\IbexaRestBundle;
use Ibexa\Bundle\Search\IbexaSearchBundle;
use Ibexa\Bundle\Solr\IbexaSolrBundle;
use Ibexa\Bundle\Taxonomy\IbexaTaxonomyBundle;
use Ibexa\Bundle\Test\Rest\IbexaTestRestBundle;
use Ibexa\Bundle\User\IbexaUserBundle;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\Core\Persistence\Content\Language\Handler as LanguageHandler;
use Ibexa\Contracts\Core\Repository\LanguageResolver;
use Ibexa\Contracts\Core\Search\Handler;
use Ibexa\Contracts\Core\Test\Persistence\Fixture\YamlFixture;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadataRegistryInterface;
use Ibexa\Contracts\Migration\Metadata\Storage\MetadataStorage;
use Ibexa\Contracts\Migration\MigrationStorage;
use Ibexa\Contracts\ProductCatalog\AssetServiceInterface;
use Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\AttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\AttributeTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\CatalogServiceInterface;
use Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface;
use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\LocalAssetServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\LocalAttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\LocalAttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\LocalProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\LocalProductTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\ProductAvailabilityServiceInterface;
use Ibexa\Contracts\ProductCatalog\ProductPriceServiceInterface;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\ProductTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\RegionServiceInterface;
use Ibexa\Contracts\ProductCatalog\VatServiceInterface;
use Ibexa\Contracts\Test\Core\IbexaTestKernel as BaseIbexaTestKernel;
use Ibexa\Core\Helper\FieldHelper;
use Ibexa\Core\Search\Common\Indexer;
use Ibexa\Elasticsearch\ElasticSearch\Client\ClientFactoryInterface;
use Ibexa\Migration\Metadata\Storage\InMemoryMetadataStorage;
use Ibexa\Migration\Reference\CollectorInterface;
use Ibexa\Migration\Storage\InMemoryMigrationStorage;
use Ibexa\ProductCatalog\Bridge\CatalogDataProvider;
use Ibexa\ProductCatalog\FieldType;
use Ibexa\ProductCatalog\Local\Persistence;
use Ibexa\ProductCatalog\Local\Repository\CustomerGroupService;
use Ibexa\ProductCatalog\Local\Repository\ProductSpecificationLocator;
use Ibexa\ProductCatalog\Migrations\Attribute\AttributeMigrationGenerator;
use Ibexa\ProductCatalog\Migrations\AttributeGroup\AttributeGroupMigrationGenerator;
use Ibexa\ProductCatalog\Personalization\Storage\ProductDataSource;
use Ibexa\Rest\Server\Controller\JWT;
use Ibexa\Solr\Handler as SolrHandler;
use Ibexa\Tests\Integration\ProductCatalog\DependencyInjection\Configuration\SiteAccessAware\IgnoredConfigParser;
use Ibexa\Tests\Integration\ProductCatalog\Fixtures\ContentCustomerGroupFixture;
use Ibexa\Tests\Integration\ProductCatalog\Fixtures\CurrencyFixture;
use Ibexa\Tests\Integration\ProductCatalog\Fixtures\CustomerGroupFixture;
use Ibexa\Tests\Integration\ProductCatalog\Fixtures\ProductPriceFixture;
use Lexik\Bundle\JWTAuthenticationBundle\LexikJWTAuthenticationBundle;
use Overblog\GraphQLBundle\OverblogGraphQLBundle;
use Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle;
use Swift_Mailer;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Serializer\SerializerInterface;

final class IbexaTestKernel extends BaseIbexaTestKernel
{
    public function getSchemaFiles(): iterable
    {
        yield from parent::getSchemaFiles();

        yield from [
            $this->locateResource('@IbexaProductCatalogBundle/Resources/config/schema.yaml'),
        ];
    }

    public function getFixtures(): iterable
    {
        yield from parent::getFixtures();

        yield new YamlFixture(__DIR__ . '/_fixtures/attribute_groups_data.yaml');
        yield new YamlFixture(__DIR__ . '/_fixtures/attribute_definitions_data.yaml');
        yield new YamlFixture(__DIR__ . '/_fixtures/product_specification_asset.yaml');

        yield new CurrencyFixture();
        yield new CustomerGroupFixture();
        yield new ContentCustomerGroupFixture();
        yield new ProductPriceFixture();
    }

    public function registerBundles(): iterable
    {
        yield from parent::registerBundles();

        yield from [
            new IbexaCorePersistenceBundle(),
            new IbexaTestRestBundle(),
            new IbexaSolrBundle(),
            new IbexaElasticsearchBundle(),
            new IbexaMigrationBundle(),
            new IbexaRestBundle(),
            new IbexaGraphQLBundle(),
            new IbexaFieldTypeRichTextBundle(),
            new IbexaFieldTypeMatrixBundle(),
            new IbexaProductCatalogBundle(true),
            new IbexaTaxonomyBundle(),
            new DAMADoctrineTestBundle(),
            new OverblogGraphQLBundle(),
            new LexikJWTAuthenticationBundle(),
            new StofDoctrineExtensionsBundle(),
            new IbexaPersonalizationBundle(),
            new IbexaAdminUiBundle(),
            new IbexaContentFormsBundle(),
            new IbexaSearchBundle(),
            new HautelookTemplatedUriBundle(),
            new IbexaUserBundle(),
            new IbexaNotificationsBundle(),
        ];
    }

    protected static function getExposedServicesByClass(): iterable
    {
        yield from parent::getExposedServicesByClass();

        yield LanguageResolver::class;
        yield CustomerGroupService::class;
        yield Persistence\Legacy\CustomerGroup\Gateway\DoctrineDatabase::class;
        yield Persistence\Legacy\CustomerGroup\HandlerInterface::class;
        yield FieldType\CustomerGroup\Persistence\Gateway\StorageGateway::class;
        yield FieldType\CustomerGroup\Persistence\Gateway\Storage::class;
        yield AttributeGroupServiceInterface::class;
        yield AttributeTypeServiceInterface::class;
        yield AttributeDefinitionServiceInterface::class;
        yield ProductServiceInterface::class;
        yield ProductTypeServiceInterface::class;
        yield ProductAvailabilityServiceInterface::class;
        yield CatalogServiceInterface::class;
        yield FieldType\ProductSpecification\Persistence\Gateway\StorageGateway::class;
        yield FieldType\ProductSpecification\Persistence\Gateway\Storage::class;
        yield Persistence\Legacy\Attribute\GatewayInterface::class;
        yield Persistence\Legacy\ProductPrice\Gateway\DoctrineDatabase::class;
        yield Persistence\Legacy\ProductPrice\Mapper::class;
        yield Persistence\Legacy\ProductPrice\Handler::class;
        yield ProductPriceServiceInterface::class;
        yield Persistence\Legacy\Currency\Gateway\DoctrineDatabase::class;
        yield LocalProductServiceInterface::class;
        yield LocalProductTypeServiceInterface::class;
        yield LocalAttributeGroupServiceInterface::class;
        yield LocalAttributeDefinitionServiceInterface::class;
        yield RegionServiceInterface::class;
        yield VatServiceInterface::class;
        yield CustomerGroupServiceInterface::class;
        yield CurrencyServiceInterface::class;
        yield ClientFactoryInterface::class;
        yield DoctrineSchemaMetadataRegistryInterface::class;
        yield Persistence\Legacy\Attribute\Handler::class;
        yield CatalogDataProvider::class;
        yield AttributeMigrationGenerator::class;
        yield AttributeGroupMigrationGenerator::class;
        yield FieldHelper::class;
        yield AssetServiceInterface::class;
        yield LocalAssetServiceInterface::class;
        yield ProductDataSource::class;
        yield Persistence\Legacy\ProductTypeSettings\GatewayInterface::class;
        yield Persistence\Legacy\ProductTypeSettings\HandlerInterface::class;
        yield ProductSpecificationLocator::class;

        // Used in migrations
        yield CollectorInterface::class;
        yield LanguageHandler::class;
    }

    protected static function getExposedServicesById(): iterable
    {
        yield from parent::getExposedServicesById();

        yield 'ibexa.spi.search' => Handler::class;
        yield Indexer::class => Indexer::class;
        yield SolrHandler::class => Handler::class;

        yield 'ibexa.migrations.serializer' => SerializerInterface::class;
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        parent::registerContainerConfiguration($loader);

        $loader->load(__DIR__ . '/Resources/ibexa.yaml');
        $loader->load(__DIR__ . '/Resources/ibexa_elasticsearch.yaml');
        $loader->load(__DIR__ . '/Resources/ibexa_product_catalog.yaml');
        $loader->load(__DIR__ . '/Resources/ibexa_solr.yaml');
        $loader->load(__DIR__ . '/Resources/lexik_jwt_authentication.yaml');
        $loader->load(__DIR__ . '/Resources/stof_doctrine_extensions.yaml');
        $loader->load(__DIR__ . '/Resources/services_test.yaml');

        $loader->load(static function (ContainerBuilder $container): void {
            $container->setParameter('form.type_extension.csrf.enabled', false);

            self::prepareIbexaFramework($container);
            self::prepareDatabaseConnection($container);
            self::prepareCommerceServices($container);
            self::createSyntheticAdminUiServices($container);
            self::addSyntheticService($container, RecommendationController::class);
        });
    }

    private static function prepareDatabaseConnection(ContainerBuilder $container): void
    {
        $container->setDefinition(MetadataStorage::class, new Definition(InMemoryMetadataStorage::class));
        $container->setDefinition(MigrationStorage::class, new Definition(InMemoryMigrationStorage::class));
    }

    private static function prepareIbexaFramework(ContainerBuilder $container): void
    {
        $container->loadFromExtension('framework', [
            'router' => [
                'resource' => __DIR__ . '/Resources/routing.yaml',
            ],
        ]);

        $container->setParameter('locale_fallback', 'en');

        /** @var \Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension $kernel */
        $kernel = $container->getExtension('ibexa');
        $kernel->addConfigParser(new IgnoredConfigParser([
            'design',
            'search_view',
            'admin_ui_forms',
            'content_create_view',
            'content_translate_view',
            'content_edit_view',
            'limitation_value_templates',
            'universal_discovery_widget_module',
            'user_settings_update_view',
        ]));
    }

    private static function createSyntheticAdminUiServices(ContainerBuilder $container): void
    {
        self::addSyntheticService($container, TranslatableNotificationHandlerInterface::class);
        self::addSyntheticService($container, SubmitHandler::class);
        self::addSyntheticService($container, MenuItemFactory::class);
        self::addSyntheticService($container, ContentTypeDraftMapper::class);
        self::addSyntheticService($container, FieldTypeToolbarFactory::class);
        self::addSyntheticService($container, JWT::class);
        self::addSyntheticService($container, AdminExceptionListener::class);
        self::addSyntheticService($container, Swift_Mailer::class);
        self::addSyntheticService($container, CommerceSiteConfig::class);
        self::addSyntheticService($container, ContentTypeMappings::class);
        self::addSyntheticService($container, ContentViewController::class);
    }

    private static function prepareCommerceServices(ContainerBuilder $container): void
    {
        self::addSyntheticService($container, BaseCatalogUrl::class);
        self::addSyntheticService($container, BasketRepository::class);
    }
}
