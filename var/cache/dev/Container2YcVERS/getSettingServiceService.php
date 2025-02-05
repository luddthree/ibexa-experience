<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getSettingServiceService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\Core\Event\SettingService' shared autowired service.
     *
     * @return \Ibexa\Core\Event\SettingService
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/contracts/Repository/SettingService.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/contracts/Repository/Decorator/SettingServiceDecorator.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/lib/Event/SettingService.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/lib/Repository/SettingService.php';

        $a = ($container->privates['Ibexa\\Core\\Persistence\\Cache\\SettingHandler'] ?? $container->getSettingHandlerService());

        if (isset($container->privates['Ibexa\\Core\\Event\\SettingService'])) {
            return $container->privates['Ibexa\\Core\\Event\\SettingService'];
        }
        $b = ($container->privates['Ibexa\\Core\\Repository\\Permission\\CachedPermissionService'] ?? $container->getCachedPermissionServiceService());

        if (isset($container->privates['Ibexa\\Core\\Event\\SettingService'])) {
            return $container->privates['Ibexa\\Core\\Event\\SettingService'];
        }
        $c = ($container->services['event_dispatcher'] ?? $container->getEventDispatcherService());

        if (isset($container->privates['Ibexa\\Core\\Event\\SettingService'])) {
            return $container->privates['Ibexa\\Core\\Event\\SettingService'];
        }

        return $container->privates['Ibexa\\Core\\Event\\SettingService'] = new \Ibexa\Core\Event\SettingService(new \Ibexa\Core\Repository\SettingService($a, $b), $c);
    }
}
