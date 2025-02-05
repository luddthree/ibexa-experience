<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getChartControllerService extends App_KernelDevDebugContainer
{
    /**
     * Gets the public 'Ibexa\Bundle\Personalization\Controller\ChartController' shared autowired service.
     *
     * @return \Ibexa\Bundle\Personalization\Controller\ChartController
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/framework-bundle/Controller/AbstractController.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/bundle/Core/Controller.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/personalization/src/bundle/Controller/AbstractPersonalizationAjaxController.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/personalization/src/bundle/Controller/ChartController.php';

        $container->services['Ibexa\\Bundle\\Personalization\\Controller\\ChartController'] = $instance = new \Ibexa\Bundle\Personalization\Controller\ChartController(($container->privates['Ibexa\\Personalization\\Permission\\PermissionChecker'] ?? $container->load('getPermissionChecker2Service')), ($container->privates['Ibexa\\Personalization\\Service\\Setting\\DefaultSiteAccessSettingService'] ?? $container->load('getDefaultSiteAccessSettingServiceService')), ($container->privates['Ibexa\\Personalization\\Service\\Chart\\ChartService'] ?? $container->load('getChartServiceService')), ($container->privates['Ibexa\\Personalization\\Factory\\Form\\PersonalizationFormFactory'] ?? $container->load('getPersonalizationFormFactoryService')));

        $instance->setContainer(($container->privates['.service_locator.GS60sXn'] ?? $container->load('get_ServiceLocator_GS60sXnService'))->withContext('Ibexa\\Bundle\\Personalization\\Controller\\ChartController', $container));

        return $instance;
    }
}
