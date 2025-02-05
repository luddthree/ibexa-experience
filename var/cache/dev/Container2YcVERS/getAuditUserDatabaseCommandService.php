<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getAuditUserDatabaseCommandService extends App_KernelDevDebugContainer
{
    /**
     * Gets the public 'console.command.public_alias.Ibexa\Bundle\User\Command\AuditUserDatabaseCommand' shared autowired service.
     *
     * @return \Ibexa\Bundle\User\Command\AuditUserDatabaseCommand
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/console/Command/Command.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/bundle/Core/Command/BackwardCompatibleCommand.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/user/src/bundle/Command/AuditUserDatabaseCommand.php';

        $container->services['console.command.public_alias.Ibexa\\Bundle\\User\\Command\\AuditUserDatabaseCommand'] = $instance = new \Ibexa\Bundle\User\Command\AuditUserDatabaseCommand(($container->services['ibexa.api.service.content_type'] ?? $container->getIbexa_Api_Service_ContentTypeService()), ($container->services['ibexa.api.service.user'] ?? $container->getIbexa_Api_Service_UserService()), ($container->services['ibexa.persistence.connection'] ?? $container->getIbexa_Persistence_ConnectionService()));

        $instance->addOption('siteaccess', NULL, 4, 'SiteAccess to use for operations. If not provided, default siteaccess will be used');

        return $instance;
    }
}
