<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Seo;

use DAMA\DoctrineTestBundle\DAMADoctrineTestBundle;
use Hautelook\TemplatedUriBundle\HautelookTemplatedUriBundle;
use Ibexa\AdminUi\EventListener\AdminExceptionListener;
use Ibexa\AdminUi\Menu\MenuItemFactory;
use Ibexa\Bundle\AdminUi\IbexaAdminUiBundle;
use Ibexa\Bundle\ContentForms\IbexaContentFormsBundle;
use Ibexa\Bundle\DesignEngine\IbexaDesignEngineBundle;
use Ibexa\Bundle\Migration\IbexaMigrationBundle;
use Ibexa\Bundle\Notifications\IbexaNotificationsBundle;
use Ibexa\Bundle\Rest\IbexaRestBundle;
use Ibexa\Bundle\Search\IbexaSearchBundle;
use Ibexa\Bundle\Seo\IbexaSeoBundle;
use Ibexa\Bundle\User\Controller\PasswordResetController;
use Ibexa\Bundle\User\IbexaUserBundle;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\Migration\Metadata\Storage\MetadataStorage;
use Ibexa\Contracts\Migration\MigrationService;
use Ibexa\Contracts\Test\Core\IbexaTestKernel;
use Ibexa\Migration\Generator\Content\StepBuilder\Create;
use Ibexa\Migration\Generator\StepBuilder\ContentTypeStepFactory;
use Ibexa\Rest\Server\Controller;
use Ibexa\Rest\Server\Controller\JWT;
use LogicException;
use Swift_Mailer;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @internal
 */
final class TestKernel extends IbexaTestKernel
{
    public function registerBundles(): iterable
    {
        yield from parent::registerBundles();

        yield new IbexaAdminUiBundle();
        yield new IbexaDesignEngineBundle();
        yield new IbexaContentFormsBundle();
        yield new IbexaUserBundle();
        yield new IbexaSearchBundle();
        yield new IbexaMigrationBundle();
        yield new IbexaNotificationsBundle();

        yield new IbexaRestBundle();
        yield new HautelookTemplatedUriBundle();
        yield new DAMADoctrineTestBundle();

        yield new IbexaSeoBundle();
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        parent::registerContainerConfiguration($loader);

        $loader->load(static function (ContainerBuilder $container): void {
            $container->setParameter('locale_fallback', 'en');

            $container->loadFromExtension('framework', [
                'router' => [
                    'resource' => __DIR__ . '/Resources/routing.yaml',
                ],
            ]);
            $container->addResource(new FileResource(__DIR__ . '/Resources/routing.yaml'));

            self::createSyntheticService($container, Controller::class);
            self::createSyntheticService($container, AdminExceptionListener::class);
            self::createSyntheticService($container, JWT::class);
            self::createSyntheticService($container, MenuItemFactory::class);
            self::createSyntheticService($container, PasswordResetController::class);
            self::createSyntheticService($container, Swift_Mailer::class);
        });

        $loader->load(dirname(__DIR__, 2) . '/src/bundle/Resources/config/services.yaml');
    }

    protected static function getExposedServicesById(): iterable
    {
        yield from parent::getExposedServicesById();

        yield 'ibexa.migrations.serializer' => NormalizerInterface::class;
    }

    protected static function getExposedServicesByClass(): iterable
    {
        yield from parent::getExposedServicesByClass();

        yield FormFactoryInterface::class;

        yield ContentTypeStepFactory::class;
        yield Create::class;

        yield MigrationService::class;

        yield MetadataStorage::class;
    }

    /**
     * Creates synthetic services in container, allowing compilation of container when some services are missing.
     * Additionally, those services can be replaced with mock implementations at runtime, allowing integration testing.
     *
     * You can set them up in KernelTestCase by calling `self::getContainer()->set($id, $this->createMock($class));`
     *
     * @phpstan-param class-string $class
     */
    private static function createSyntheticService(ContainerBuilder $container, string $class, ?string $id = null): void
    {
        $id = $id ?? $class;
        if ($container->has($id)) {
            throw new LogicException(sprintf(
                'Expected test kernel to not contain "%s" service. A real service should not be overwritten by a mock',
                TranslatableNotificationHandlerInterface::class,
            ));
        }

        $definition = new Definition($class);
        $definition->setSynthetic(true);
        $container->setDefinition($id, $definition);
    }
}
