<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getUrlRedirectProcessorService extends App_KernelDevDebugContainer
{
    /**
     * Gets the public 'Ibexa\AdminUi\Form\Processor\Content\UrlRedirectProcessor' shared autowired service.
     *
     * @return \Ibexa\AdminUi\Form\Processor\Content\UrlRedirectProcessor
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/lib/Form/Processor/Content/UrlRedirectProcessor.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/content-forms/src/lib/Form/Processor/SystemUrlRedirectProcessor.php';

        $a = ($container->services['router'] ?? $container->getRouterService());

        if (isset($container->services['Ibexa\\AdminUi\\Form\\Processor\\Content\\UrlRedirectProcessor'])) {
            return $container->services['Ibexa\\AdminUi\\Form\\Processor\\Content\\UrlRedirectProcessor'];
        }
        $b = ($container->services['ibexa.api.service.location'] ?? $container->getIbexa_Api_Service_LocationService());

        if (isset($container->services['Ibexa\\AdminUi\\Form\\Processor\\Content\\UrlRedirectProcessor'])) {
            return $container->services['Ibexa\\AdminUi\\Form\\Processor\\Content\\UrlRedirectProcessor'];
        }

        return $container->services['Ibexa\\AdminUi\\Form\\Processor\\Content\\UrlRedirectProcessor'] = new \Ibexa\AdminUi\Form\Processor\Content\UrlRedirectProcessor(($container->privates['Ibexa\\Core\\MVC\\Symfony\\SiteAccess'] ?? ($container->privates['Ibexa\\Core\\MVC\\Symfony\\SiteAccess'] = new \Ibexa\Core\MVC\Symfony\SiteAccess('default', 'uninitialized'))), new \Ibexa\ContentForms\Form\Processor\SystemUrlRedirectProcessor($a, ($container->services['ibexa.api.service.url_alias'] ?? $container->getIbexa_Api_Service_UrlAliasService()), $b), $container->parameters['ibexa.site_access.groups']);
    }
}
