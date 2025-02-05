<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getAttributesTabService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\ProductCatalog\Tab\AttributeGroup\AttributesTab' shared autowired service.
     *
     * @return \Ibexa\ProductCatalog\Tab\AttributeGroup\AttributesTab
     */
    public static function do($container, $lazyLoad = true)
    {
        if ($lazyLoad) {
            return $container->privates['Ibexa\\ProductCatalog\\Tab\\AttributeGroup\\AttributesTab'] = $container->createProxy('AttributesTab_b169354', function () use ($container) {
                return \AttributesTab_b169354::staticProxyConstructor(function (&$wrappedInstance, \ProxyManager\Proxy\LazyLoadingInterface $proxy) use ($container) {
                    $wrappedInstance = self::do($container, false);

                    $proxy->setProxyInitializer(null);

                    return true;
                });
            });
        }

        include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/contracts/Tab/TabInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/contracts/Tab/AbstractTab.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/contracts/Tab/AbstractEventDispatchingTab.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/contracts/Tab/OrderedTabInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/product-catalog/src/lib/Tab/AttributeGroup/AttributesTab.php';

        return new \Ibexa\ProductCatalog\Tab\AttributeGroup\AttributesTab(($container->services['.container.private.twig'] ?? $container->get_Container_Private_TwigService()), ($container->services['Symfony\\Contracts\\Translation\\TranslatorInterface'] ?? $container->getTranslatorInterfaceService()), ($container->services['event_dispatcher'] ?? $container->getEventDispatcherService()), ($container->privates['Ibexa\\ProductCatalog\\Dispatcher\\AttributeDefinitionServiceDispatcher'] ?? $container->getAttributeDefinitionServiceDispatcherService()), ($container->services['.container.private.form.factory'] ?? $container->get_Container_Private_Form_FactoryService()), ($container->services['router'] ?? $container->getRouterService()), ($container->services['Ibexa\\Bundle\\Core\\DependencyInjection\\Configuration\\ChainConfigResolver'] ?? $container->getChainConfigResolverService()));
    }
}
