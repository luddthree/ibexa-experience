<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Segmentation;

use DAMA\DoctrineTestBundle\DAMADoctrineTestBundle;
use FOS\HttpCacheBundle\FOSHttpCacheBundle;
use Hautelook\TemplatedUriBundle\HautelookTemplatedUriBundle;
use Hautelook\TemplatedUriRouter\Routing\Generator\Rfc6570Generator;
use Ibexa\AdminUi\EventListener\AdminExceptionListener;
use Ibexa\AdminUi\Menu\MenuItemFactory;
use Ibexa\Bundle\AdminUi\IbexaAdminUiBundle;
use Ibexa\Bundle\Calendar\IbexaCalendarBundle;
use Ibexa\Bundle\ContentForms\IbexaContentFormsBundle;
use Ibexa\Bundle\Core\Routing\DefaultRouter;
use Ibexa\Bundle\DesignEngine\IbexaDesignEngineBundle;
use Ibexa\Bundle\FieldTypePage\IbexaFieldTypePageBundle;
use Ibexa\Bundle\FieldTypeRichText\IbexaFieldTypeRichTextBundle;
use Ibexa\Bundle\HttpCache\IbexaHttpCacheBundle;
use Ibexa\Bundle\Migration\IbexaMigrationBundle;
use Ibexa\Bundle\Notifications\IbexaNotificationsBundle;
use Ibexa\Bundle\PageBuilder\IbexaPageBuilderBundle;
use Ibexa\Bundle\Rest\IbexaRestBundle;
use Ibexa\Bundle\Search\IbexaSearchBundle;
use Ibexa\Bundle\Segmentation\IbexaSegmentationBundle;
use Ibexa\Bundle\User\Controller\PasswordResetController;
use Ibexa\Bundle\User\IbexaUserBundle;
use Ibexa\Contracts\Core\Test\Persistence\Fixture\YamlFixture;
use Ibexa\Contracts\Segmentation\SegmentationServiceInterface;
use Ibexa\Contracts\Test\Core\IbexaTestKernel;
use Ibexa\Migration\Reference\CollectorInterface;
use Ibexa\Rest\Server\Controller;
use Ibexa\Rest\Server\Controller\JWT;
use Swift_Mailer;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Serializer\SerializerInterface;

final class TestKernel extends IbexaTestKernel
{
    public function registerBundles(): iterable
    {
        yield from parent::registerBundles();

        yield new DAMADoctrineTestBundle();

        yield new IbexaMigrationBundle();
        yield new IbexaHttpCacheBundle();
        yield new FOSHttpCacheBundle();
        yield new IbexaFieldTypePageBundle();
        yield new IbexaPageBuilderBundle();
        yield new IbexaAdminUiBundle();
        yield new IbexaDesignEngineBundle();
        yield new IbexaContentFormsBundle();
        yield new IbexaUserBundle();
        yield new IbexaSearchBundle();
        yield new IbexaCalendarBundle();
        yield new IbexaRestBundle();
        yield new IbexaFieldTypeRichTextBundle();
        yield new IbexaNotificationsBundle();
        yield new HautelookTemplatedUriBundle();

        yield new IbexaSegmentationBundle();
    }

    protected static function getExposedServicesByClass(): iterable
    {
        yield from parent::getExposedServicesByClass();

        yield SegmentationServiceInterface::class;
        yield CollectorInterface::class;
    }

    protected static function getExposedServicesById(): iterable
    {
        yield from parent::getExposedServicesById();

        yield 'ibexa.migrations.serializer' => SerializerInterface::class;
    }

    public function getSchemaFiles(): iterable
    {
        yield from parent::getSchemaFiles();

        yield $this->locateResource('@IbexaSegmentationBundle/Resources/config/storage/legacy/schema.yaml');
    }

    public function getFixtures(): iterable
    {
        yield from parent::getFixtures();

        yield new YamlFixture(__DIR__ . '/Resources/fixtures/ibexa_segment_groups.yaml');
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        parent::registerContainerConfiguration($loader);

        $loader->load(__DIR__ . '/Resources/fos_http_cache.yaml');
        $loader->load(__DIR__ . '/Resources/framework.yaml');
        $loader->load(__DIR__ . '/Resources/ibexa.yaml');

        $loader->load(static function (ContainerBuilder $container): void {
            $container->setParameter('locale_fallback', 'en');
            $container->setParameter('form.type_extension.csrf.enabled', false);

            self::addSyntheticService($container, Controller::class);
            self::addSyntheticService($container, AdminExceptionListener::class);
            self::addSyntheticService($container, JWT::class);
            self::addSyntheticService($container, MenuItemFactory::class);
            self::addSyntheticService($container, PasswordResetController::class);
            self::addSyntheticService($container, Swift_Mailer::class);
            self::addSyntheticService($container, Rfc6570Generator::class, DefaultRouter::class);

            $container->loadFromExtension('framework', [
                'router' => [
                    'resource' => __DIR__ . '/Resources/routing.yaml',
                ],
            ]);
        });
    }
}
