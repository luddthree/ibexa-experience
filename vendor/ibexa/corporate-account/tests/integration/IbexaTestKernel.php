<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\CorporateAccount;

use DAMA\DoctrineTestBundle\DAMADoctrineTestBundle;
use FOS\HttpCacheBundle\FOSHttpCacheBundle;
use Hautelook\TemplatedUriBundle\HautelookTemplatedUriBundle;
use Ibexa\AdminUi\EventListener\AdminExceptionListener;
use Ibexa\AdminUi\Menu\MenuItemFactory;
use Ibexa\Bundle\AdminUi\IbexaAdminUiBundle;
use Ibexa\Bundle\Commerce\Checkout\Entity\BasketRepository;
use Ibexa\Bundle\Commerce\Eshop\Api\Configuration\CommerceSiteConfig;
use Ibexa\Bundle\Commerce\Eshop\Services\Url\BaseCatalogUrl;
use Ibexa\Bundle\ContentForms\IbexaContentFormsBundle;
use Ibexa\Bundle\CorePersistence\IbexaCorePersistenceBundle;
use Ibexa\Bundle\CorporateAccount\IbexaCorporateAccountBundle;
use Ibexa\Bundle\DesignEngine\IbexaDesignEngineBundle;
use Ibexa\Bundle\FieldTypeAddress\IbexaFieldTypeAddressBundle;
use Ibexa\Bundle\FieldTypeMatrix\IbexaFieldTypeMatrixBundle;
use Ibexa\Bundle\FieldTypeRichText\IbexaFieldTypeRichTextBundle;
use Ibexa\Bundle\GraphQL\IbexaGraphQLBundle;
use Ibexa\Bundle\HttpCache\IbexaHttpCacheBundle;
use Ibexa\Bundle\Migration\IbexaMigrationBundle;
use Ibexa\Bundle\Notifications\IbexaNotificationsBundle;
use Ibexa\Bundle\Personalization\Controller\RecommendationController;
use Ibexa\Bundle\Personalization\IbexaPersonalizationBundle;
use Ibexa\Bundle\ProductCatalog\EventSubscriber\MainMenuSubscriber;
use Ibexa\Bundle\ProductCatalog\Form\CatalogFilter\ProductCategoryFormMapper;
use Ibexa\Bundle\ProductCatalog\IbexaProductCatalogBundle;
use Ibexa\Bundle\Rest\IbexaRestBundle;
use Ibexa\Bundle\Search\IbexaSearchBundle;
use Ibexa\Bundle\Segmentation\IbexaSegmentationBundle;
use Ibexa\Bundle\Taxonomy\IbexaTaxonomyBundle;
use Ibexa\Bundle\Test\Rest\IbexaTestRestBundle;
use Ibexa\Bundle\User\IbexaUserBundle;
use Ibexa\Contracts\Core\Test\Persistence\Fixture;
use Ibexa\Contracts\Core\Test\Persistence\Fixture\YamlFixture;
use Ibexa\Contracts\CorporateAccount\Service\CompanyService;
use Ibexa\Contracts\CorporateAccount\Service\CorporateAccountService;
use Ibexa\Contracts\CorporateAccount\Service\MemberService;
use Ibexa\Contracts\CorporateAccount\Service\SalesRepresentativesService;
use Ibexa\Contracts\CorporateAccount\Service\ShippingAddressService;
use Ibexa\Contracts\Migration\Metadata\Storage\MetadataStorage;
use Ibexa\Contracts\Migration\MigrationService;
use Ibexa\Contracts\Migration\MigrationStorage;
use Ibexa\Contracts\Test\Core\IbexaTestKernel as BaseIbexaTestKernel;
use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use Ibexa\CorporateAccount\Form\ActionDispatcher\CompanyDispatcher;
use Ibexa\CorporateAccount\Form\ActionDispatcher\MemberDispatcher;
use Ibexa\CorporateAccount\Form\ActionDispatcher\ShippingAddressDispatcher;
use Ibexa\CorporateAccount\Form\CompanyFormFactory;
use Ibexa\CorporateAccount\Form\MemberFormFactory;
use Ibexa\CorporateAccount\Form\ShippingAddressFormFactory;
use Ibexa\Migration\Metadata\Storage\InMemoryMetadataStorage;
use Ibexa\Migration\Reference\CollectorInterface;
use Ibexa\Migration\Repository\Migration;
use Ibexa\Migration\Service\FieldTypeServiceInterface;
use Ibexa\Migration\StepExecutor\StepExecutorManager;
use Ibexa\Migration\Storage\InMemoryMigrationStorage;
use Ibexa\ProductCatalog\GraphQL\Schema\Worker\Attribute\AddProductAttributes;
use Ibexa\Rest\Server\Controller\JWT;
use Ibexa\Tests\Integration\CorporateAccount\DependencyInjection\Configuration\IgnoredConfigParser;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle;
use Swift_Mailer;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class IbexaTestKernel extends BaseIbexaTestKernel
{
    public function getSchemaFiles(): iterable
    {
        yield from parent::getSchemaFiles();

        yield from [
            $this->locateResource('@IbexaCorporateAccountBundle/Resources/config/schema.yaml'),
            $this->locateResource('@IbexaProductCatalogBundle/Resources/config/schema.yaml'),
            $this->locateResource('@IbexaSegmentationBundle/Resources/config/storage/legacy/schema.yaml'),
        ];
    }

    public function getFixtures(): iterable
    {
        $coreFixtures = [];
        foreach (parent::getFixtures() as $coreFixture) {
            $coreFixtures = array_merge_recursive($coreFixtures, $coreFixture->load());
        }

        yield new class($coreFixtures) implements Fixture {
            /**
             * @var array<string, array<string, mixed>>
             */
            private array $coreFixtures;

            /**
             * @param array<string, array<string, mixed>> $coreFixtures
             */
            public function __construct(array $coreFixtures)
            {
                $this->coreFixtures = $coreFixtures;
            }

            /**
             * @return array<string, array<string, mixed>>
             */
            public function load(): array
            {
                return array_merge_recursive(
                    $this->coreFixtures,
                    (new YamlFixture(__DIR__ . '/_fixtures/customer_groups.yaml'))->load(),
                    (new YamlFixture(__DIR__ . '/_fixtures/test_company.yaml'))->load()
                );
            }
        };
    }

    public function registerBundles(): iterable
    {
        yield from parent::registerBundles();

        yield from [
            new DAMADoctrineTestBundle(),

            new IbexaCorePersistenceBundle(),
            new IbexaAdminUiBundle(),
            new IbexaRestBundle(),
            new IbexaDesignEngineBundle(),
            new IbexaContentFormsBundle(),
            new IbexaSearchBundle(),
            new IbexaSegmentationBundle(),
            new IbexaHttpCacheBundle(),
            new FOSHttpCacheBundle(),
            new HautelookTemplatedUriBundle(),
            new IbexaProductCatalogBundle(),
            new IbexaGraphQLBundle(),
            new IbexaFieldTypeRichTextBundle(),
            new IbexaMigrationBundle(),
            new IbexaUserBundle(),
            new IbexaFieldTypeAddressBundle(),
            new IbexaTaxonomyBundle(),
            new IbexaCorporateAccountBundle(),
            new IbexaPersonalizationBundle(),
            new IbexaNotificationsBundle(),
            new IbexaFieldTypeMatrixBundle(),
            new IbexaTestRestBundle(),
            new SensioFrameworkExtraBundle(),
        ];
    }

    protected static function getExposedServicesByClass(): iterable
    {
        yield from parent::getExposedServicesByClass();

        yield CorporateAccountConfiguration::class;
        yield CorporateAccountService::class;
        yield CompanyService::class;
        yield CompanyFormFactory::class;
        yield CompanyDispatcher::class;
        yield MemberService::class;
        yield MemberDispatcher::class;
        yield MemberFormFactory::class;
        yield SalesRepresentativesService::class;
        yield ShippingAddressService::class;
        yield ShippingAddressDispatcher::class;
        yield ShippingAddressFormFactory::class;

        yield StepExecutorManager::class;

        yield FieldTypeServiceInterface::class;
        yield CollectorInterface::class;
    }

    protected static function getExposedServicesById(): iterable
    {
        yield from parent::getExposedServicesById();

        yield 'ibexa.migrations.io.flysystem.default_filesystem' => FilesystemOperator::class;
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        parent::registerContainerConfiguration($loader);

        $loader->load(__DIR__ . '/Resources/ibexa.yaml');
        $loader->load(__DIR__ . '/Resources/fos_http_cache.yaml');
        $loader->load(__DIR__ . '/Resources/liip_imagine.yaml');

        $loader->load(static function (ContainerBuilder $container): void {
            self::prepareIbexaFramework($container);

            $container->setParameter('locale_fallback', 'en');
            $container->setParameter('ibexa.http_cache.purge_type', 'local');

            $container->setParameter('form.type_extension.csrf.enabled', false);
            $container->setParameter('ibexa.test.rest.schema.directory', __DIR__ . '/Resources/REST/schemas');

            $container->setDefinition(MetadataStorage::class, new Definition(InMemoryMetadataStorage::class));
            $container->setDefinition(MigrationStorage::class, new Definition(InMemoryMigrationStorage::class));
            self::overwriteMigrationsFilesystemAdapter($container);

            self::addSyntheticService($container, AdminExceptionListener::class);
            self::addSyntheticService($container, JWT::class);
            self::addSyntheticService($container, MenuItemFactory::class);
            self::addSyntheticService($container, Swift_Mailer::class);

            self::addSyntheticService($container, BaseCatalogUrl::class);
            self::addSyntheticService($container, BasketRepository::class);
            self::addSyntheticService($container, CommerceSiteConfig::class);
            self::addSyntheticService($container, MainMenuSubscriber::class);
            self::addSyntheticService($container, ProductCategoryFormMapper::class);
            self::addSyntheticService($container, AddProductAttributes::class);
            self::addSyntheticService($container, RecommendationController::class);
        });
    }

    public function executeMigration(string $name): void
    {
        $path = __DIR__ . '/_migrations/' . $name;

        $content = file_get_contents($path);
        if ($content !== false) {
            /** @var \Symfony\Component\DependencyInjection\ContainerInterface $testContainer */
            $testContainer = $this->getContainer()->get('test.service_container');
            /** @var \Ibexa\Contracts\Migration\MigrationService $migrationService */
            $migrationService = $testContainer->get(MigrationService::class);
            $migrationService->executeOne(new Migration(uniqid('migration', true), $content));
        } else {
            throw new \LogicException(sprintf('Unable to load "%s" fixture', $path));
        }
    }

    private static function prepareIbexaFramework(ContainerBuilder $container): void
    {
        $container->loadFromExtension('framework', [
            'router' => [
                'resource' => __DIR__ . '/Resources/routing.yaml',
            ],
        ]);

        /** @var \Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension $kernel */
        $kernel = $container->getExtension('ibexa');
        $kernel->addConfigParser(new IgnoredConfigParser([
            'page_builder_forms',
        ]));
    }

    private static function overwriteMigrationsFilesystemAdapter(ContainerBuilder $container): void
    {
        $definition = new Definition(InMemoryFilesystemAdapter::class);
        $definition->setPublic(true);
        $container->setDefinition(
            self::getAliasServiceId('ibexa.migrations.flysystem_memory_adapter'),
            $definition
        );

        $container->setAlias(
            'ibexa.migrations.io.flysystem.default_adapter',
            self::getAliasServiceId('ibexa.migrations.flysystem_memory_adapter')
        );
    }
}
