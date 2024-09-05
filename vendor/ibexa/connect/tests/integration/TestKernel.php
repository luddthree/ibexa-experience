<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Connect;

use DAMA\DoctrineTestBundle\DAMADoctrineTestBundle;
use Gregwar\CaptchaBundle\GregwarCaptchaBundle;
use Hautelook\TemplatedUriBundle\HautelookTemplatedUriBundle;
use Ibexa\Bundle\AdminUi\IbexaAdminUiBundle;
use Ibexa\Bundle\Connect\IbexaConnectBundle;
use Ibexa\Bundle\ContentForms\IbexaContentFormsBundle;
use Ibexa\Bundle\DesignEngine\IbexaDesignEngineBundle;
use Ibexa\Bundle\FormBuilder\IbexaFormBuilderBundle;
use Ibexa\Bundle\Notifications\IbexaNotificationsBundle;
use Ibexa\Bundle\Rest\IbexaRestBundle;
use Ibexa\Bundle\Search\IbexaSearchBundle;
use Ibexa\Bundle\Test\Core\IbexaTestCoreBundle;
use Ibexa\Bundle\Test\Rest\IbexaTestRestBundle;
use Ibexa\Bundle\User\IbexaUserBundle;
use Ibexa\Contracts\Test\Core\IbexaTestKernel;
use Ibexa\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinitionFactoryInterface;
use Ibexa\FormBuilder\FieldType\FormFactory;
use Ibexa\Tests\Integration\Connect\DependencyInjection\Configuration\IgnoredConfigParser;
use Knp\Bundle\MenuBundle\KnpMenuBundle;
use Lexik\Bundle\JWTAuthenticationBundle\LexikJWTAuthenticationBundle;
use Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\WebpackEncoreBundle\WebpackEncoreBundle;

final class TestKernel extends IbexaTestKernel
{
    public function registerBundles(): iterable
    {
        yield from parent::registerBundles();

        yield new IbexaTestCoreBundle();
        yield new IbexaTestRestBundle();

        yield new IbexaFormBuilderBundle();

        yield new DAMADoctrineTestBundle();

        yield new IbexaAdminUiBundle();
        yield new IbexaDesignEngineBundle();
        yield new IbexaRestBundle();
        yield new IbexaContentFormsBundle();
        yield new IbexaSearchBundle();
        yield new IbexaUserBundle();
        yield new IbexaNotificationsBundle();

        yield new GregwarCaptchaBundle();
        yield new HautelookTemplatedUriBundle();
        yield new WebpackEncoreBundle();
        yield new LexikJWTAuthenticationBundle();
        yield new SwiftmailerBundle();
        yield new KnpMenuBundle();

        yield new IbexaConnectBundle();
    }

    protected static function getExposedServicesByClass(): iterable
    {
        yield from parent::getExposedServicesByClass();

        yield FormFactory::class;
    }

    protected static function getExposedServicesById(): iterable
    {
        yield from parent::getExposedServicesById();

        yield 'ibexa.connect.http_client' => HttpClientInterface::class;
    }

    public function getSchemaFiles(): iterable
    {
        yield from parent::getSchemaFiles();

        yield $this->locateResource('@IbexaFormBuilderBundle/Resources/config/schema.yaml');
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        parent::registerContainerConfiguration($loader);

        $loader->load(__DIR__ . '/Resources/framework.yaml');
        $loader->load(__DIR__ . '/Resources/ibexa.yaml');
        $loader->load(__DIR__ . '/Resources/ibexa_connect.yaml');
        $loader->load(__DIR__ . '/Resources/services.yaml');

        $loader->load(static function (ContainerBuilder $container): void {
            $container->loadFromExtension('framework', [
                'router' => [
                    'resource' => __DIR__ . '/Resources/routing.yaml',
                ],
            ]);

            /** @var \Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension $kernel */
            $kernel = $container->getExtension('ibexa');
            $kernel->addConfigParser(new IgnoredConfigParser([
                'design',
                'page_builder_forms',
            ]));

            $container->setParameter('locale_fallback', 'en');
            $container->setDefinition(
                'Symfony\Component\Notifier\NotifierInterface',
                new Definition(NotifierInterface::class)
            );
            self::addSyntheticService($container, BlockDefinitionFactoryInterface::class);
        });
    }
}
