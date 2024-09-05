<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\FieldTypePage;

use DAMA\DoctrineTestBundle\DAMADoctrineTestBundle;
use FOS\HttpCacheBundle\FOSHttpCacheBundle;
use Hautelook\TemplatedUriBundle\HautelookTemplatedUriBundle;
use Ibexa\AdminUi\Menu\MenuItemFactory;
use Ibexa\Bundle\AdminUi\IbexaAdminUiBundle;
use Ibexa\Bundle\Calendar\IbexaCalendarBundle;
use Ibexa\Bundle\ContentForms\IbexaContentFormsBundle;
use Ibexa\Bundle\DesignEngine\IbexaDesignEngineBundle;
use Ibexa\Bundle\FieldTypePage\IbexaFieldTypePageBundle;
use Ibexa\Bundle\FieldTypeRichText\IbexaFieldTypeRichTextBundle;
use Ibexa\Bundle\HttpCache\IbexaHttpCacheBundle;
use Ibexa\Bundle\Migration\IbexaMigrationBundle;
use Ibexa\Bundle\Notifications\IbexaNotificationsBundle;
use Ibexa\Bundle\Rest\IbexaRestBundle;
use Ibexa\Bundle\Search\IbexaSearchBundle;
use Ibexa\Bundle\SiteContext\IbexaSiteContextBundle;
use Ibexa\Bundle\User\IbexaUserBundle;
use Ibexa\ContentForms\Form\ActionDispatcher\UserDispatcher;
use Ibexa\Contracts\AdminUi\Notification\NotificationHandlerInterface;
use Ibexa\Contracts\Migration\Metadata\Storage\MetadataStorage;
use Ibexa\Contracts\Migration\MigrationService;
use Ibexa\Contracts\Migration\MigrationStorage;
use Ibexa\Contracts\Test\Core\DependencyInjection\Configuration\SiteAccessAware\IgnoredConfigParser;
use Ibexa\Contracts\Test\Core\IbexaTestKernel;
use Ibexa\FieldTypePage\Migration\Action\PutBlockOntoPage\PutBlockOntoPageActionExecutor;
use Ibexa\Migration\Metadata\Storage\InMemoryMetadataStorage;
use Ibexa\Migration\Repository\Migration;
use Ibexa\Migration\Storage\InMemoryMigrationStorage;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Swift_Mailer;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\Fragment\EsiFragmentRenderer;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookupCollection;
use Symfony\WebpackEncoreBundle\Asset\TagRenderer;

/**
 * @internal
 */
final class FieldTypePageIbexaTestKernel extends IbexaTestKernel
{
    public function getSchemaFiles(): iterable
    {
        yield from parent::getSchemaFiles();

        yield $this->locateResource('@IbexaFieldTypePageBundle/Resources/config/storage/schema.yaml');
    }

    public function registerBundles(): iterable
    {
        yield from parent::registerBundles();

        yield new HautelookTemplatedUriBundle();
        yield new FOSHttpCacheBundle();

        yield new DAMADoctrineTestBundle();

        yield new IbexaCalendarBundle();
        yield new IbexaRestBundle();
        yield new IbexaHttpCacheBundle();
        yield new IbexaFieldTypeRichTextBundle();
        yield new IbexaUserBundle();
        yield new IbexaMigrationBundle();
        yield new IbexaNotificationsBundle();
        yield new IbexaAdminUiBundle();
        yield new IbexaDesignEngineBundle();
        yield new IbexaSiteContextBundle();
        yield new IbexaContentFormsBundle();
        yield new IbexaSearchBundle();

        yield new IbexaFieldTypePageBundle();
    }

    protected static function getExposedServicesByClass(): iterable
    {
        yield from parent::getExposedServicesByClass();

        yield PutBlockOntoPageActionExecutor::class;

        yield MigrationService::class;
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        parent::registerContainerConfiguration($loader);

        $loader->load(static function (ContainerBuilder $container): void {
            self::configureIbexaDXP($container);
            self::configureThirdPartyPackages($container);
            self::createSyntheticDerivativeServices($container);
        });
    }

    public function executeMigration(string $name): void
    {
        $path = __DIR__ . '/Migration/_migrations/' . $name;

        $content = file_get_contents($path);
        if ($content !== false) {
            /** @var \Symfony\Component\DependencyInjection\ContainerInterface $testContainer */
            $testContainer = $this->getContainer()->get('test.service_container');
            /** @var \Ibexa\Contracts\Migration\MigrationService $migrationService */
            $migrationService = $testContainer->get(MigrationService::class);
            $migrationService->executeOne(new Migration(uniqid($name, true), $content));
        } else {
            throw new \LogicException(sprintf('Unable to load "%s" fixture', $path));
        }
    }

    private static function configureIbexaDXP(ContainerBuilder $container): void
    {
        $container->setParameter('ibexa.http_cache.purge_type', 'local');
        $container->setParameter('fos_http_cache.compiler_pass.tag_annotations', false);

        $container->setDefinition(MetadataStorage::class, new Definition(InMemoryMetadataStorage::class));
        $container->setDefinition(MigrationStorage::class, new Definition(InMemoryMigrationStorage::class));

        self::addSyntheticService($container, UserDispatcher::class);
        self::addSyntheticService($container, MenuItemFactory::class);
        self::addSyntheticService($container, NotificationHandlerInterface::class);

        $container->loadFromExtension('framework', [
            'router' => [
                'resource' => __DIR__ . '/Resources/config/routing.yaml',
            ],
        ]);

        /** @var \Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension $core */
        $core = $container->getExtension('ibexa');
        $core->addConfigParser(new IgnoredConfigParser(
            'admin_ui_forms',
            'content_create_view',
            'content_translate_view',
            'content_edit_view',
            'limitation_value_templates',
            'universal_discovery_widget_module',
            'page_builder_forms',
            'design',
            'user_registration',
            'user_settings_update_view',
            'search_view'
        ));
    }

    private static function configureThirdPartyPackages(ContainerBuilder $container): void
    {
        $container->setParameter('locale_fallback', 'en');
        $container->register('fragment.renderer.esi', EsiFragmentRenderer::class);
        self::addSyntheticService($container, JWTTokenManagerInterface::class);
        self::addSyntheticService($container, Swift_Mailer::class);
    }

    private static function createSyntheticDerivativeServices(ContainerBuilder $container): void
    {
        self::addSyntheticService($container, TagRenderer::class, 'webpack_encore.tag_renderer');
        self::addSyntheticService(
            $container,
            EntrypointLookupCollection::class,
            'webpack_encore.entrypoint_lookup_collection'
        );
    }
}
