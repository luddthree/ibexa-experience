<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Personalization;

use DAMA\DoctrineTestBundle\DAMADoctrineTestBundle;
use FOS\HttpCacheBundle\FOSHttpCacheBundle;
use GuzzleHttp\Handler\MockHandler;
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
use Ibexa\Bundle\Personalization\IbexaPersonalizationBundle;
use Ibexa\Bundle\Rest\IbexaRestBundle;
use Ibexa\Bundle\Search\IbexaSearchBundle;
use Ibexa\Bundle\Segmentation\IbexaSegmentationBundle;
use Ibexa\Bundle\User\IbexaUserBundle;
use Ibexa\Contracts\Core\Repository\SettingService as ApiSettingService;
use Ibexa\Contracts\Core\Repository\TokenService;
use Ibexa\Contracts\Migration\Metadata\Storage\MetadataStorage;
use Ibexa\Contracts\Migration\MigrationService;
use Ibexa\Contracts\Migration\MigrationStorage;
use Ibexa\Contracts\Test\Core\IbexaTestKernel as BaseIbexaTestKernel;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Migration\Metadata\Storage\InMemoryMetadataStorage;
use Ibexa\Migration\Storage\InMemoryMigrationStorage;
use Ibexa\Personalization\Authentication\AuthenticationInterface;
use Ibexa\Personalization\Config\Authentication\ParametersResolverInterface;
use Ibexa\Personalization\Config\Host\HostResolverInterface;
use Ibexa\Personalization\Config\ItemType\IncludedItemTypeResolverInterface;
use Ibexa\Personalization\Content\DataResolverInterface;
use Ibexa\Personalization\Content\Routing\UrlGeneratorInterface;
use Ibexa\Personalization\Service\Content\ContentServiceInterface;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use Ibexa\Personalization\SiteAccess\ScopeParameterResolver;
use Ibexa\Personalization\Storage\ContentDataSource;
use Ibexa\Rest\Server\Controller\JWT;
use Ibexa\Tests\Integration\Personalization\DependencyInjection\Configuration\IgnoredConfigParser;
use Liip\ImagineBundle\Binary\Locator\FileSystemLocator;
use Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle;
use Swift_Mailer;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\Fragment\EsiFragmentRenderer;
use Symfony\WebpackEncoreBundle\WebpackEncoreBundle;

final class IbexaTestKernel extends BaseIbexaTestKernel
{
    /**
     * @return iterable<\Symfony\Component\HttpKernel\Bundle\BundleInterface>
     */
    public function registerBundles(): iterable
    {
        yield from parent::registerBundles();

        yield from [
            new DAMADoctrineTestBundle(),

            new IbexaAdminUiBundle(),
            new IbexaContentFormsBundle(),
            new IbexaSearchBundle(),
            new IbexaPageBuilderBundle(),
            new IbexaFieldTypePageBundle(),
            new IbexaRestBundle(),
            new IbexaSegmentationBundle(),
            new IbexaFieldTypeRichTextBundle(),
            new IbexaHttpCacheBundle(),
            new IbexaUserBundle(),
            new IbexaMigrationBundle(),
            new IbexaNotificationsBundle(),
            new IbexaPersonalizationBundle(),

            new HautelookTemplatedUriBundle(),
            new FOSHttpCacheBundle(),
            new SensioFrameworkExtraBundle(),
            new WebpackEncoreBundle(),
        ];
    }

    protected static function getExposedServicesByClass(): iterable
    {
        yield from parent::getExposedServicesByClass();

        yield from [
            ApiSettingService::class,
            AuthenticationInterface::class,
            ContentServiceInterface::class,
            ContentDataSource::class,
            DataResolverInterface::class,
            HostResolverInterface::class,
            IncludedItemTypeResolverInterface::class,
            ParametersResolverInterface::class,
            ScopeParameterResolver::class,
            SettingServiceInterface::class,
            SiteAccessServiceInterface::class,
            UrlGeneratorInterface::class,
            TokenService::class,
            MigrationService::class,
        ];
    }

    protected static function getExposedServicesById(): iterable
    {
        yield from parent::getExposedServicesById();

        yield 'ibexa.personalization.http_client_handler_mock.test' => MockHandler::class;
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        parent::registerContainerConfiguration($loader);

        $loader->load(__DIR__ . '/Resources/ibexa.yaml');
        $loader->load(__DIR__ . '/Resources/ibexa_personalization.yaml');
        $loader->load(__DIR__ . '/Resources/liip_imagine.yaml');
        $loader->load(__DIR__ . '/Resources/services.yaml');

        $loader->load(static function (ContainerBuilder $container): void {
            self::prepareIbexaDXP($container);
            self::prepareWebpackEncore($container);
            self::prepareMigrationStorage($container);

            self::addSyntheticService($container, FileSystemLocator::class);
            self::addSyntheticService($container, JWT::class);
            self::addSyntheticService($container, AdminExceptionListener::class);
            self::addSyntheticService($container, MenuItemFactory::class);
            self::addSyntheticService($container, Swift_Mailer::class);

            $container->loadFromExtension('framework', [
                'router' => [
                    'resource' => __DIR__ . '/Resources/routing.yaml',
                ],
            ]);
        });
    }

    private static function prepareIbexaDXP(ContainerBuilder $container): void
    {
        $container->setParameter('ibexa.http_cache.purge_type', 'local');
        $container->setParameter('kernel.secret', 'secret');
        $container->setParameter('locale_fallback', 'en');
        $container->register('fragment.renderer.esi', EsiFragmentRenderer::class);

        /** @var \Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension $kernel */
        $kernel = $container->getExtension('ibexa');
        $kernel->addConfigParser(new IgnoredConfigParser([
            'calendar',
            'design',
            'search_view',
            'admin_ui_forms',
            'content_create_view',
            'content_translate_view',
            'content_edit_view',
            'limitation_value_templates',
            'universal_discovery_widget_module',
        ]));
    }

    private static function prepareWebpackEncore(ContainerBuilder $container): void
    {
        $container->loadFromExtension('webpack_encore', [
            'output_path' => '%kernel.project_dir%/public/build',
        ]);
    }

    private static function prepareMigrationStorage(ContainerBuilder $container): void
    {
        $container->setDefinition(MetadataStorage::class, new Definition(InMemoryMetadataStorage::class));
        $container->setDefinition(MigrationStorage::class, new Definition(InMemoryMigrationStorage::class));
    }
}
