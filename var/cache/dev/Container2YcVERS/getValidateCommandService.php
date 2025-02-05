<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getValidateCommandService extends App_KernelDevDebugContainer
{
    /**
     * Gets the public 'Overblog\GraphQLBundle\Command\ValidateCommand' shared service.
     *
     * @return \Overblog\GraphQLBundle\Command\ValidateCommand
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/console/Command/Command.php';
        include_once \dirname(__DIR__, 4).'/vendor/overblog/graphql-bundle/src/Command/ValidateCommand.php';

        $container->services['Overblog\\GraphQLBundle\\Command\\ValidateCommand'] = $instance = new \Overblog\GraphQLBundle\Command\ValidateCommand(($container->services['overblog_graphql.request_executor'] ?? $container->load('getOverblogGraphql_RequestExecutorService')));

        $instance->addOption('siteaccess', NULL, 4, 'SiteAccess to use for operations. If not provided, default siteaccess will be used');
        $instance->setName('graphql:validate');

        return $instance;
    }
}
