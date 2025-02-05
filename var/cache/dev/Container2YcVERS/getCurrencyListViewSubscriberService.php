<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getCurrencyListViewSubscriberService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\Bundle\ProductCatalog\EventSubscriber\CurrencyListViewSubscriber' shared autowired service.
     *
     * @return \Ibexa\Bundle\ProductCatalog\EventSubscriber\CurrencyListViewSubscriber
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/product-catalog/src/bundle/EventSubscriber/CurrencyListViewSubscriber.php';

        $a = ($container->services['.container.private.form.factory'] ?? $container->get_Container_Private_Form_FactoryService());

        if (isset($container->privates['Ibexa\\Bundle\\ProductCatalog\\EventSubscriber\\CurrencyListViewSubscriber'])) {
            return $container->privates['Ibexa\\Bundle\\ProductCatalog\\EventSubscriber\\CurrencyListViewSubscriber'];
        }
        $b = ($container->services['router'] ?? $container->getRouterService());

        if (isset($container->privates['Ibexa\\Bundle\\ProductCatalog\\EventSubscriber\\CurrencyListViewSubscriber'])) {
            return $container->privates['Ibexa\\Bundle\\ProductCatalog\\EventSubscriber\\CurrencyListViewSubscriber'];
        }

        return $container->privates['Ibexa\\Bundle\\ProductCatalog\\EventSubscriber\\CurrencyListViewSubscriber'] = new \Ibexa\Bundle\ProductCatalog\EventSubscriber\CurrencyListViewSubscriber($a, $b, ($container->privates['Ibexa\\ProductCatalog\\Local\\Permission\\PermissionResolver'] ?? $container->getPermissionResolverService()));
    }
}
