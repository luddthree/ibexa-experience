<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getCopyConfigurationToSettingsActionExecutorService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\Installer\Migration\CopyConfigurationToSettingsActionExecutor' shared autowired service.
     *
     * @return \Ibexa\Installer\Migration\CopyConfigurationToSettingsActionExecutor
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/migrations/src/lib/StepExecutor/ActionExecutor/ExecutorInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/installer/src/lib/Migration/CopyConfigurationToSettingsActionExecutor.php';

        return $container->privates['Ibexa\\Installer\\Migration\\CopyConfigurationToSettingsActionExecutor'] = new \Ibexa\Installer\Migration\CopyConfigurationToSettingsActionExecutor(($container->services['ibexa.api.service.content'] ?? $container->getIbexa_Api_Service_ContentService()), ($container->privates['Ibexa\\Core\\Event\\SettingService'] ?? $container->load('getSettingServiceService')));
    }
}
