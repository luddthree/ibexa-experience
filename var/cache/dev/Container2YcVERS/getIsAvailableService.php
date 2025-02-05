<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getIsAvailableService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\Contracts\ProductCatalog\ViewMatcher\ProductBased\IsAvailable' shared autowired service.
     *
     * @return \Ibexa\Bundle\ProductCatalog\View\Matcher\ProductBased\IsAvailable
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/lib/MVC/Symfony/Matcher/ViewMatcherInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/lib/MVC/Symfony/Matcher/ContentBased/MatcherInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/product-catalog/src/bundle/View/Matcher/ProductBased/AbstractProductMatcher.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/product-catalog/src/contracts/ViewMatcher/ProductBased/IsAvailable.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/product-catalog/src/bundle/View/Matcher/ProductBased/IsAvailable.php';

        $a = ($container->privates['Ibexa\\ProductCatalog\\Local\\Repository\\Event\\ProductService'] ?? $container->load('getProductServiceService'));

        if (isset($container->privates['Ibexa\\Contracts\\ProductCatalog\\ViewMatcher\\ProductBased\\IsAvailable'])) {
            return $container->privates['Ibexa\\Contracts\\ProductCatalog\\ViewMatcher\\ProductBased\\IsAvailable'];
        }
        $b = ($container->privates['Ibexa\\ProductCatalog\\Local\\Repository\\Event\\ProductAvailabilityService'] ?? $container->getProductAvailabilityServiceService());

        if (isset($container->privates['Ibexa\\Contracts\\ProductCatalog\\ViewMatcher\\ProductBased\\IsAvailable'])) {
            return $container->privates['Ibexa\\Contracts\\ProductCatalog\\ViewMatcher\\ProductBased\\IsAvailable'];
        }

        return $container->privates['Ibexa\\Contracts\\ProductCatalog\\ViewMatcher\\ProductBased\\IsAvailable'] = new \Ibexa\Bundle\ProductCatalog\View\Matcher\ProductBased\IsAvailable($a, $b);
    }
}
