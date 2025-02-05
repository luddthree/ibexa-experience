<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getRootLocationProviderService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\CorporateAccount\PageBuilder\SiteAccess\RootLocationProvider' shared autowired service.
     *
     * @return \Ibexa\CorporateAccount\PageBuilder\SiteAccess\RootLocationProvider
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/corporate-account/src/lib/PageBuilder/SiteAccess/RootLocationProvider.php';

        $a = ($container->services['ibexa.api.service.location'] ?? $container->getIbexa_Api_Service_LocationService());

        if (isset($container->privates['Ibexa\\CorporateAccount\\PageBuilder\\SiteAccess\\RootLocationProvider'])) {
            return $container->privates['Ibexa\\CorporateAccount\\PageBuilder\\SiteAccess\\RootLocationProvider'];
        }

        return $container->privates['Ibexa\\CorporateAccount\\PageBuilder\\SiteAccess\\RootLocationProvider'] = new \Ibexa\CorporateAccount\PageBuilder\SiteAccess\RootLocationProvider(($container->services['Ibexa\\Bundle\\Core\\DependencyInjection\\Configuration\\ChainConfigResolver'] ?? $container->getChainConfigResolverService()), $a);
    }
}
