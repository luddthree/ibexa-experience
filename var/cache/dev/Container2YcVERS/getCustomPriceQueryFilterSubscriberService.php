<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getCustomPriceQueryFilterSubscriberService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\Bundle\ProductCatalog\EventSubscriber\CustomPriceQueryFilterSubscriber' shared autowired service.
     *
     * @return \Ibexa\Bundle\ProductCatalog\EventSubscriber\CustomPriceQueryFilterSubscriber
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/product-catalog/src/bundle/EventSubscriber/CustomPriceQueryFilterSubscriber.php';

        return $container->privates['Ibexa\\Bundle\\ProductCatalog\\EventSubscriber\\CustomPriceQueryFilterSubscriber'] = new \Ibexa\Bundle\ProductCatalog\EventSubscriber\CustomPriceQueryFilterSubscriber(($container->privates['Ibexa\\ProductCatalog\\Local\\Repository\\ChainCustomerGroupResolver'] ?? $container->getChainCustomerGroupResolverService()));
    }
}
