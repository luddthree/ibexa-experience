<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\FormBuilder;

use FOS\HttpCacheBundle\FOSHttpCacheBundle;
use Gregwar\CaptchaBundle\GregwarCaptchaBundle;
use Hautelook\TemplatedUriBundle\HautelookTemplatedUriBundle;
use Ibexa\Bundle\AdminUi\IbexaAdminUiBundle;
use Ibexa\Bundle\Calendar\IbexaCalendarBundle;
use Ibexa\Bundle\ContentForms\IbexaContentFormsBundle;
use Ibexa\Bundle\DesignEngine\IbexaDesignEngineBundle;
use Ibexa\Bundle\FieldTypePage\IbexaFieldTypePageBundle;
use Ibexa\Bundle\FieldTypeRichText\IbexaFieldTypeRichTextBundle;
use Ibexa\Bundle\FormBuilder\IbexaFormBuilderBundle;
use Ibexa\Bundle\HttpCache\IbexaHttpCacheBundle;
use Ibexa\Bundle\Notifications\IbexaNotificationsBundle;
use Ibexa\Bundle\Rest\IbexaRestBundle;
use Ibexa\Bundle\Search\IbexaSearchBundle;
use Ibexa\Bundle\User\IbexaUserBundle;
use Ibexa\Contracts\Test\Core\IbexaTestKernel;
use Ibexa\Tests\Integration\FormBuilder\DependencyInjection\Configuration\SiteAccessAware\IgnoredConfigParser;
use Knp\Bundle\MenuBundle\KnpMenuBundle;
use Lexik\Bundle\JWTAuthenticationBundle\LexikJWTAuthenticationBundle;
use Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle;
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
        yield new GregwarCaptchaBundle();
        yield new FOSHttpCacheBundle();

        yield new IbexaRestBundle();
        yield new IbexaContentFormsBundle();
        yield new IbexaSearchBundle();
        yield new IbexaUserBundle();
        yield new IbexaDesignEngineBundle();
        yield new IbexaAdminUiBundle();
        yield new IbexaNotificationsBundle();
        yield new IbexaFieldTypePageBundle();
        yield new IbexaCalendarBundle();
        yield new IbexaHttpCacheBundle();
        yield new IbexaFieldTypeRichTextBundle();

        yield new IbexaFormBuilderBundle();
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        parent::registerContainerConfiguration($loader);

        $loader->load(__DIR__ . '/Resources/config.yaml');
        $loader->load(static function (ContainerBuilder $container): void {
            $container->register('fragment.renderer.esi', EsiFragmentRenderer::class);
            $container->setParameter('ibexa.http_cache.purge_type', 'local');
            $resource = new FileResource(__DIR__ . '/Resources/routing.yaml');
            $container->addResource($resource);
            $container->setParameter('form.type_extension.csrf.enabled', false);
            $container->loadFromExtension('framework', [
                'router' => [
                    'resource' => $resource->getResource(),
                ],
            ]);
            self::prepareIbexaFramework($container);
        });
    }

    private static function prepareIbexaFramework(ContainerBuilder $container): void
    {
        /** @var \Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension $kernel */
        $kernel = $container->getExtension('ibexa');
        $kernel->addConfigParser(new IgnoredConfigParser([
            'page_builder_forms',
        ]));
    }
}
