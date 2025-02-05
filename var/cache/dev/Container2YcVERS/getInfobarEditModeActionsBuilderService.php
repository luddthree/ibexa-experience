<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getInfobarEditModeActionsBuilderService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\Bundle\Dashboard\Menu\InfobarEditModeActionsBuilder' shared autowired service.
     *
     * @return \Ibexa\Bundle\Dashboard\Menu\InfobarEditModeActionsBuilder
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/contracts/Menu/AbstractBuilder.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/dashboard/src/bundle/Menu/InfobarEditModeActionsBuilder.php';

        $a = ($container->services['Ibexa\\AdminUi\\Menu\\MenuItemFactory'] ?? $container->load('getMenuItemFactoryService'));

        if (isset($container->privates['Ibexa\\Bundle\\Dashboard\\Menu\\InfobarEditModeActionsBuilder'])) {
            return $container->privates['Ibexa\\Bundle\\Dashboard\\Menu\\InfobarEditModeActionsBuilder'];
        }
        $b = ($container->services['event_dispatcher'] ?? $container->getEventDispatcherService());

        if (isset($container->privates['Ibexa\\Bundle\\Dashboard\\Menu\\InfobarEditModeActionsBuilder'])) {
            return $container->privates['Ibexa\\Bundle\\Dashboard\\Menu\\InfobarEditModeActionsBuilder'];
        }
        $c = ($container->privates['Ibexa\\Core\\Repository\\Permission\\CachedPermissionService'] ?? $container->getCachedPermissionServiceService());

        if (isset($container->privates['Ibexa\\Bundle\\Dashboard\\Menu\\InfobarEditModeActionsBuilder'])) {
            return $container->privates['Ibexa\\Bundle\\Dashboard\\Menu\\InfobarEditModeActionsBuilder'];
        }

        return $container->privates['Ibexa\\Bundle\\Dashboard\\Menu\\InfobarEditModeActionsBuilder'] = new \Ibexa\Bundle\Dashboard\Menu\InfobarEditModeActionsBuilder($a, $b, $c);
    }
}
