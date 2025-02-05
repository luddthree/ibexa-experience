<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getMyContentTabService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\AdminUi\Tab\Dashboard\MyContentTab' shared autowired service.
     *
     * @return \Ibexa\AdminUi\Tab\Dashboard\MyContentTab
     */
    public static function do($container, $lazyLoad = true)
    {
        if ($lazyLoad) {
            return $container->privates['Ibexa\\AdminUi\\Tab\\Dashboard\\MyContentTab'] = $container->createProxy('MyContentTab_2f2332d', function () use ($container) {
                return \MyContentTab_2f2332d::staticProxyConstructor(function (&$wrappedInstance, \ProxyManager\Proxy\LazyLoadingInterface $proxy) use ($container) {
                    $wrappedInstance = self::do($container, false);

                    $proxy->setProxyInitializer(null);

                    return true;
                });
            });
        }

        include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/contracts/Tab/TabInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/contracts/Tab/AbstractTab.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/contracts/Tab/OrderedTabInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/lib/Tab/Dashboard/AbstractContentTab.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/lib/Tab/Dashboard/MyContentTab.php';

        return new \Ibexa\AdminUi\Tab\Dashboard\MyContentTab(($container->services['.container.private.twig'] ?? $container->get_Container_Private_TwigService()), ($container->services['Symfony\\Contracts\\Translation\\TranslatorInterface'] ?? $container->getTranslatorInterfaceService()), ($container->privates['Ibexa\\AdminUi\\Tab\\Dashboard\\PagerLocationToDataMapper'] ?? $container->load('getPagerLocationToDataMapperService')), ($container->services['ibexa.api.service.search'] ?? $container->getIbexa_Api_Service_SearchService()), ($container->privates['Ibexa\\AdminUi\\QueryType\\ContentLocationSubtreeQueryType'] ?? $container->getContentLocationSubtreeQueryTypeService()));
    }
}
