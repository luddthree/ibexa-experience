<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getProductVariantController2Service extends App_KernelDevDebugContainer
{
    /**
     * Gets the public 'Ibexa\Bundle\ProductCatalog\Controller\REST\ProductVariantController' shared autowired service.
     *
     * @return \Ibexa\Bundle\ProductCatalog\Controller\REST\ProductVariantController
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/rest/src/lib/Server/Controller.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/product-catalog/src/bundle/Controller/REST/ProductVariantController.php';

        $container->services['Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\ProductVariantController'] = $instance = new \Ibexa\Bundle\ProductCatalog\Controller\REST\ProductVariantController(($container->privates['Ibexa\\ProductCatalog\\Dispatcher\\ProductServiceDispatcher'] ?? $container->getProductServiceDispatcherService()), ($container->privates['Ibexa\\ProductCatalog\\Local\\Repository\\Event\\ProductService'] ?? $container->load('getProductServiceService')), ($container->privates['Ibexa\\ProductCatalog\\Local\\Repository\\Variant\\VariantGenerator'] ?? $container->load('getVariantGeneratorService')));

        $instance->setContainer($container);
        $instance->setInputDispatcher(($container->privates['Ibexa\\Rest\\Input\\Dispatcher'] ?? $container->getDispatcherService()));
        $instance->setRouter(($container->services['router'] ?? $container->getRouterService()));
        $instance->setRequestParser(($container->privates['Ibexa\\Bundle\\Rest\\RequestParser\\Router'] ?? $container->getRouter2Service()));
        $instance->setRepository(($container->services['ibexa.api.repository'] ?? $container->getIbexa_Api_RepositoryService()));

        return $instance;
    }
}
