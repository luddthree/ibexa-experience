<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getCompanyEditSuccessViewSubscriberService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\Bundle\CorporateAccount\EventSubscriber\CompanyEditSuccessViewSubscriber' shared autowired service.
     *
     * @return \Ibexa\Bundle\CorporateAccount\EventSubscriber\CompanyEditSuccessViewSubscriber
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/corporate-account/src/bundle/EventSubscriber/AbstractViewSubscriber.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/corporate-account/src/bundle/EventSubscriber/CompanyEditSuccessViewSubscriber.php';

        $a = ($container->services['router'] ?? $container->getRouterService());

        if (isset($container->privates['Ibexa\\Bundle\\CorporateAccount\\EventSubscriber\\CompanyEditSuccessViewSubscriber'])) {
            return $container->privates['Ibexa\\Bundle\\CorporateAccount\\EventSubscriber\\CompanyEditSuccessViewSubscriber'];
        }

        return $container->privates['Ibexa\\Bundle\\CorporateAccount\\EventSubscriber\\CompanyEditSuccessViewSubscriber'] = new \Ibexa\Bundle\CorporateAccount\EventSubscriber\CompanyEditSuccessViewSubscriber(($container->privates['Ibexa\\Core\\MVC\\Symfony\\SiteAccess\\SiteAccessService'] ?? $container->getSiteAccessServiceService()), $a);
    }
}
