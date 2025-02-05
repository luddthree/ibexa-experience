<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getRenderProductRuntimeService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\Bundle\ProductCatalog\Twig\RenderProductRuntime' shared autowired service.
     *
     * @return \Ibexa\Bundle\ProductCatalog\Twig\RenderProductRuntime
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/twig/twig/src/Extension/RuntimeExtensionInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/product-catalog/src/bundle/Twig/RenderProductRuntime.php';

        $a = ($container->privates['Ibexa\\ProductCatalog\\Local\\Repository\\Event\\ProductService'] ?? $container->load('getProductServiceService'));

        if (isset($container->privates['Ibexa\\Bundle\\ProductCatalog\\Twig\\RenderProductRuntime'])) {
            return $container->privates['Ibexa\\Bundle\\ProductCatalog\\Twig\\RenderProductRuntime'];
        }

        return $container->privates['Ibexa\\Bundle\\ProductCatalog\\Twig\\RenderProductRuntime'] = new \Ibexa\Bundle\ProductCatalog\Twig\RenderProductRuntime($a, ($container->privates['Ibexa\\ProductCatalog\\Local\\Repository\\Attribute\\ValueFormatterDispatcher'] ?? $container->getValueFormatterDispatcherService()));
    }
}
