<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ActivityLog;

use DAMA\DoctrineTestBundle\DAMADoctrineTestBundle;
use Hautelook\TemplatedUriBundle\HautelookTemplatedUriBundle;
use Ibexa\ActivityLog\Persistence\ActivityLog\GatewayInterface;
use Ibexa\ActivityLog\Persistence\ActivityLog\Group\GatewayInterface as GroupGatewayInterface;
use Ibexa\ActivityLog\Persistence\HandlerInterface;
use Ibexa\AdminUi\Limitation\Mapper\MultipleSelectionBasedMapper;
use Ibexa\Bundle\ActivityLog\ControllerArgumentResolver\ActivityLogGetQueryArgumentResolver;
use Ibexa\Bundle\ActivityLog\IbexaActivityLogBundle;
use Ibexa\Bundle\CorePersistence\IbexaCorePersistenceBundle;
use Ibexa\Bundle\Rest\IbexaRestBundle;
use Ibexa\Bundle\Test\Rest\IbexaTestRestBundle;
use Ibexa\Contracts\ActivityLog\ActivityLogServiceInterface;
use Ibexa\Contracts\Core\Test\Persistence\Fixture;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Contracts\Test\Core\IbexaTestKernel;
use Ibexa\Rest\Input\Handler\Json;
use Ibexa\Rest\Input\Handler\Xml;
use Ibexa\Rest\Server\Controller\JWT;
use Ibexa\Tests\Integration\ActivityLog\Security\PermissionResolverMock;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class Kernel extends IbexaTestKernel
{
    public function registerBundles(): iterable
    {
        yield from parent::registerBundles();

        yield new IbexaActivityLogBundle();
        yield new IbexaCorePersistenceBundle();

        yield new HautelookTemplatedUriBundle();
        yield new IbexaRestBundle();
        yield new IbexaTestRestBundle();

        yield new DAMADoctrineTestBundle();
    }

    public function getSchemaFiles(): iterable
    {
        yield from parent::getSchemaFiles();

        yield $this->locateResource('@IbexaActivityLogBundle/Resources/config/schema.yaml');
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        parent::registerContainerConfiguration($loader);

        $loader->load(__DIR__ . '/Resources/ibexa.yaml');
        $loader->load(__DIR__ . '/Resources/services.yaml');

        $loader->load(static function (ContainerBuilder $container): void {
            $resource = new FileResource(__DIR__ . '/Resources/routing.yaml');
            $container->addResource($resource);
            $container->loadFromExtension('framework', [
                'router' => [
                    'resource' => $resource->getResource(),
                ],
            ]);

            $container->setParameter('form.type_extension.csrf.enabled', false);

            self::addSyntheticService($container, JWT::class);
            self::addSyntheticService($container, MultipleSelectionBasedMapper::class);
        });
    }

    public function getFixtures(): iterable
    {
        yield from parent::getFixtures();

        yield new Fixture\YamlFixture(__DIR__ . '/Resources/Fixtures/activity_log.yaml');
    }

    protected static function getExposedServicesByClass(): iterable
    {
        yield from parent::getExposedServicesByClass();

        yield ActivityLogServiceInterface::class;
        yield GatewayInterface::class;
        yield GroupGatewayInterface::class;
        yield HandlerInterface::class;
        yield ActivityLogGetQueryArgumentResolver::class;

        yield ParsingDispatcher::class;
        yield Xml::class;
        yield Json::class;

        yield PermissionResolverMock::class;
    }
}
