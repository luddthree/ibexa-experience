<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\SiteFactory;

use Doctrine\DBAL\Driver\PDO\Connection;
use FOS\HttpCacheBundle\FOSHttpCacheBundle;
use Hautelook\TemplatedUriBundle\HautelookTemplatedUriBundle;
use Ibexa\Bundle\AdminUi\IbexaAdminUiBundle;
use Ibexa\Bundle\Calendar\IbexaCalendarBundle;
use Ibexa\Bundle\ContentForms\IbexaContentFormsBundle;
use Ibexa\Bundle\DesignEngine\IbexaDesignEngineBundle;
use Ibexa\Bundle\FieldTypePage\IbexaFieldTypePageBundle;
use Ibexa\Bundle\FieldTypeRichText\IbexaFieldTypeRichTextBundle;
use Ibexa\Bundle\HttpCache\IbexaHttpCacheBundle;
use Ibexa\Bundle\Notifications\IbexaNotificationsBundle;
use Ibexa\Bundle\PageBuilder\IbexaPageBuilderBundle;
use Ibexa\Bundle\Rest\IbexaRestBundle;
use Ibexa\Bundle\Search\IbexaSearchBundle;
use Ibexa\Bundle\SiteFactory\IbexaSiteFactoryBundle;
use Ibexa\Bundle\User\IbexaUserBundle;
use Ibexa\Contracts\Test\Core\IbexaTestKernel;
use Knp\Bundle\MenuBundle\KnpMenuBundle;
use Lexik\Bundle\JWTAuthenticationBundle\LexikJWTAuthenticationBundle;
use Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle;
use Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Fragment\EsiFragmentRenderer;
use Symfony\WebpackEncoreBundle\WebpackEncoreBundle;

final class Kernel extends IbexaTestKernel
{
    public function registerBundles(): iterable
    {
        yield from parent::registerBundles();

        yield new LexikJWTAuthenticationBundle();
        yield new HautelookTemplatedUriBundle();
        yield new WebpackEncoreBundle();
        yield new SwiftmailerBundle();
        yield new KnpMenuBundle();

        yield new IbexaRestBundle();
        yield new IbexaContentFormsBundle();
        yield new IbexaSearchBundle();
        yield new IbexaUserBundle();
        yield new IbexaDesignEngineBundle();
        yield new IbexaAdminUiBundle();
        yield new IbexaNotificationsBundle();
        yield new IbexaPageBuilderBundle();
        yield new IbexaFieldTypePageBundle();
        yield new IbexaSiteFactoryBundle();
        yield new IbexaCalendarBundle();
        yield new IbexaHttpCacheBundle();
        yield new FOSHttpCacheBundle();
        yield new SensioFrameworkExtraBundle();
        yield new IbexaFieldTypeRichTextBundle();
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        parent::registerContainerConfiguration($loader);

        $loader->load(__DIR__ . '/Resources/config.yaml');
        $loader->load(static function (ContainerBuilder $container): void {
            $container->register('fragment.renderer.esi', EsiFragmentRenderer::class);
            $container->register('doctrine.dbal.site_factory_connection', Connection::class);
            $container->register('site_factory_pool', TagAwareAdapter::class);

            $resource = new FileResource(__DIR__ . '/Resources/routing.yaml');
            $container->addResource($resource);
            $container->loadFromExtension('framework', [
                'router' => [
                    'resource' => $resource->getResource(),
                ],
            ]);
        });
    }
}
