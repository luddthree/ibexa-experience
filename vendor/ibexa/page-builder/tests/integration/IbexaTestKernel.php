<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\PageBuilder;

use DAMA\DoctrineTestBundle\DAMADoctrineTestBundle;
use FOS\HttpCacheBundle\FOSHttpCacheBundle;
use Hautelook\TemplatedUriBundle\HautelookTemplatedUriBundle;
use Ibexa\AdminUi\EventListener\AdminExceptionListener;
use Ibexa\AdminUi\Menu\MenuItemFactory;
use Ibexa\Bundle\AdminUi\IbexaAdminUiBundle;
use Ibexa\Bundle\ContentForms\IbexaContentFormsBundle;
use Ibexa\Bundle\FieldTypePage\IbexaFieldTypePageBundle;
use Ibexa\Bundle\FieldTypeRichText\IbexaFieldTypeRichTextBundle;
use Ibexa\Bundle\HttpCache\IbexaHttpCacheBundle;
use Ibexa\Bundle\Migration\IbexaMigrationBundle;
use Ibexa\Bundle\Notifications\IbexaNotificationsBundle;
use Ibexa\Bundle\PageBuilder\IbexaPageBuilderBundle;
use Ibexa\Bundle\Rest\IbexaRestBundle;
use Ibexa\Bundle\Search\IbexaSearchBundle;
use Ibexa\Bundle\User\IbexaUserBundle;
use Ibexa\Contracts\Migration\Metadata\Storage\MetadataStorage;
use Ibexa\Contracts\Migration\MigrationStorage;
use Ibexa\Contracts\PageBuilder\Siteaccess\Selector\SiteAccessSelector;
use Ibexa\Contracts\Test\Core\IbexaTestKernel as BaseIbexaTestKernel;
use Ibexa\Migration\Metadata\Storage\InMemoryMetadataStorage;
use Ibexa\Migration\Reference\CollectorInterface;
use Ibexa\Migration\Service\FieldTypeServiceInterface;
use Ibexa\Migration\Storage\InMemoryMigrationStorage;
use Ibexa\PageBuilder\Persistence\Legacy\MigrateRichTextNamespaces\Gateway\DoctrineDatabase;
use Ibexa\PageBuilder\Siteaccess\PageBuilderSiteAccessResolver;
use Ibexa\Rest\Server\Controller\JWT;
use Ibexa\Tests\Integration\PageBuilder\DependencyInjection\Configuration\IgnoredConfigParser;
use League\Flysystem\FilesystemOperator;
use Swift_Mailer;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\Fragment\EsiFragmentRenderer;

final class IbexaTestKernel extends BaseIbexaTestKernel
{
    public function getSchemaFiles(): iterable
    {
        yield from parent::getSchemaFiles();

        yield from [
            $this->locateResource('@IbexaFieldTypePageBundle/Resources/config/storage/schema.yaml'),
        ];
    }

    public function registerBundles(): iterable
    {
        yield from parent::registerBundles();

        yield from [
            new DAMADoctrineTestBundle(),

            new IbexaFieldTypePageBundle(),
            new IbexaAdminUiBundle(),
            new IbexaContentFormsBundle(),

            new IbexaRestBundle(),
            new HautelookTemplatedUriBundle(),
            new IbexaHttpCacheBundle(),
            new FOSHttpCacheBundle(),
            new IbexaSearchBundle(),
            new IbexaMigrationBundle(),
            new IbexaUserBundle(),
            new IbexaNotificationsBundle(),
            new IbexaFieldTypeRichTextBundle(),

            new IbexaPageBuilderBundle(),
        ];
    }

    protected static function getExposedServicesByClass(): iterable
    {
        yield from parent::getExposedServicesByClass();

        yield DoctrineDatabase::class;
        yield PageBuilderSiteAccessResolver::class;
        yield SiteAccessSelector::class;

        yield FieldTypeServiceInterface::class;
        yield CollectorInterface::class;
    }

    protected static function getExposedServicesById(): iterable
    {
        yield from parent::getExposedServicesById();

        yield 'ibexa.migrations.io.flysystem.default_filesystem' => FilesystemOperator::class;
        yield 'ibexa.cache_pool' => TagAwareAdapterInterface::class;
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        parent::registerContainerConfiguration($loader);

        $loader->load(__DIR__ . '/Resources/ibexa.yaml');
        $loader->load(__DIR__ . '/Resources/fos_http_cache.yaml');

        $loader->load(static function (ContainerBuilder $container): void {
            self::prepareIbexaDXP($container);

            $container->setDefinition(MetadataStorage::class, new Definition(InMemoryMetadataStorage::class));
            $container->setDefinition(MigrationStorage::class, new Definition(InMemoryMigrationStorage::class));

            self::addSyntheticService($container, AdminExceptionListener::class);
            self::addSyntheticService($container, JWT::class);
            self::addSyntheticService($container, MenuItemFactory::class);
            self::addSyntheticService($container, Swift_Mailer::class);
        });
    }

    private static function prepareIbexaDXP(ContainerBuilder $container): void
    {
        $container->loadFromExtension('framework', [
            'router' => [
                'resource' => __DIR__ . '/Resources/routing.yaml',
            ],
        ]);

        $container->register('fragment.renderer.esi', EsiFragmentRenderer::class);
        $container->setParameter('ibexa.http_cache.purge_type', 'local');
        $container->setParameter('locale_fallback', 'en');

        $kernel = $container->getExtension('ibexa');
        $kernel->addConfigParser(new IgnoredConfigParser([
            'calendar',
            'design',
            'search_view',
            'user_settings_update_view',
        ]));
    }
}
