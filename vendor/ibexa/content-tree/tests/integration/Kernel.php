<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ContentTree;

use FOS\HttpCacheBundle\FOSHttpCacheBundle;
use Hautelook\TemplatedUriBundle\HautelookTemplatedUriBundle;
use Ibexa\Bundle\AdminUi\IbexaAdminUiBundle;
use Ibexa\Bundle\ContentForms\IbexaContentFormsBundle;
use Ibexa\Bundle\ContentTree\IbexaContentTreeBundle;
use Ibexa\Bundle\HttpCache\IbexaHttpCacheBundle;
use Ibexa\Bundle\Notifications\IbexaNotificationsBundle;
use Ibexa\Bundle\Rest\IbexaRestBundle;
use Ibexa\Bundle\Search\IbexaSearchBundle;
use Ibexa\Bundle\User\IbexaUserBundle;
use Ibexa\Contracts\Test\Core\IbexaTestKernel;
use Ibexa\Tests\Integration\ContentTree\DependencyInjection\Configuration\IgnoredConfigParser;
use Knp\Menu\FactoryInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Swift_Mailer;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookupCollection;
use Symfony\WebpackEncoreBundle\Asset\TagRenderer;

final class Kernel extends IbexaTestKernel
{
    public function registerBundles(): iterable
    {
        yield from parent::registerBundles();

        yield new IbexaContentTreeBundle();

        yield new IbexaUserBundle();
        yield new IbexaHttpCacheBundle();
        yield new IbexaAdminUiBundle();
        yield new IbexaContentFormsBundle();
        yield new IbexaSearchBundle();
        yield new IbexaRestBundle();
        yield new HautelookTemplatedUriBundle();
        yield new FOSHttpCacheBundle();
        yield new IbexaNotificationsBundle();
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        parent::registerContainerConfiguration($loader);

        $loader->load(__DIR__ . '/Resources/ibexa.yaml');
        $loader->load(static function (ContainerBuilder $container): void {
            self::configureIbexaDXPBundles($container);
            self::configureThirdPartyBundles($container);
        });
    }

    private static function configureIbexaDXPBundles(ContainerBuilder $container): void
    {
        $container->setParameter('ibexa.http_cache.purge_type', 'local');
        $container->setParameter('ibexa.http_cache.translation_aware.enabled', false);
        $container->setParameter('locale_fallback', 'en');

        /** @var \Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension $kernel */
        $kernel = $container->getExtension('ibexa');
        $kernel->addConfigParser(
            new IgnoredConfigParser(
                [
                    'admin_ui_forms',
                    'calendar',
                    'content_create_view',
                    'content_translate_view',
                    'content_edit_view',
                    'design',
                    'search_view',
                    'universal_discovery_widget_module',
                ]
            )
        );
    }

    private static function configureThirdPartyBundles(ContainerBuilder $container): void
    {
        $container->setParameter('fos_http_cache.tag_handler.strict', false);
        $container->setParameter('fos_http_cache.compiler_pass.tag_annotations', false);

        self::addSyntheticService($container, Swift_Mailer::class);
        self::addSyntheticService($container, JWTTokenManagerInterface::class);
        self::addSyntheticService($container, FactoryInterface::class);
        self::addSyntheticService($container, TagRenderer::class, 'webpack_encore.tag_renderer');
        self::addSyntheticService(
            $container,
            EntrypointLookupCollection::class,
            'webpack_encore.entrypoint_lookup_collection'
        );
    }
}
