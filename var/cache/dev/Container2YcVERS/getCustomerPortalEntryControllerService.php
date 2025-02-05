<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getCustomerPortalEntryControllerService extends App_KernelDevDebugContainer
{
    /**
     * Gets the public 'Ibexa\Bundle\CorporateAccount\Controller\CorporatePortal\CustomerPortalEntryController' shared autowired service.
     *
     * @return \Ibexa\Bundle\CorporateAccount\Controller\CorporatePortal\CustomerPortalEntryController
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/framework-bundle/Controller/AbstractController.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/contracts/Controller/Controller.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/corporate-account/src/bundle/Controller/Controller.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/corporate-account/src/bundle/Controller/CorporatePortal/CustomerPortalEntryController.php';

        $container->services['Ibexa\\Bundle\\CorporateAccount\\Controller\\CorporatePortal\\CustomerPortalEntryController'] = $instance = new \Ibexa\Bundle\CorporateAccount\Controller\CorporatePortal\CustomerPortalEntryController(($container->privates['Ibexa\\CorporateAccount\\Configuration\\CorporateAccount'] ?? $container->getCorporateAccountService()), ($container->services['ibexa.api.service.location'] ?? $container->getIbexa_Api_Service_LocationService()), ($container->privates['Ibexa\\CorporateAccount\\CustomerPortalService'] ?? $container->load('getCustomerPortalServiceService')), ($container->services['router'] ?? $container->getRouterService()), ($container->privates['Ibexa\\CorporateAccount\\Permission\\MemberResolver'] ?? $container->load('getMemberResolverService')), ($container->privates['Ibexa\\CorporateAccount\\CustomerPortal\\CustomerPortalResolver'] ?? $container->load('getCustomerPortalResolverService')), ($container->privates['Ibexa\\Core\\MVC\\Symfony\\SiteAccess\\SiteAccessService'] ?? $container->getSiteAccessServiceService()));

        $instance->setContainer(($container->privates['.service_locator.mx0UMmY'] ?? $container->load('get_ServiceLocator_Mx0UMmYService'))->withContext('Ibexa\\Bundle\\CorporateAccount\\Controller\\CorporatePortal\\CustomerPortalEntryController', $container));

        return $instance;
    }
}
