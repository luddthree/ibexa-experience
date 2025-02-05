<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getRegionControllerService extends App_KernelDevDebugContainer
{
    /**
     * Gets the public 'Ibexa\Bundle\ProductCatalog\Controller\REST\RegionController' shared autowired service.
     *
     * @return \Ibexa\Bundle\ProductCatalog\Controller\REST\RegionController
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/rest/src/lib/Server/Controller.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/product-catalog/src/bundle/Controller/REST/RegionController.php';

        $container->services['Ibexa\\Bundle\\ProductCatalog\\Controller\\REST\\RegionController'] = $instance = new \Ibexa\Bundle\ProductCatalog\Controller\REST\RegionController(($container->privates['Ibexa\\ProductCatalog\\Local\\Repository\\RegionService'] ?? $container->getRegionServiceService()));

        $instance->setContainer($container);
        $instance->setInputDispatcher(($container->privates['Ibexa\\Rest\\Input\\Dispatcher'] ?? $container->getDispatcherService()));
        $instance->setRouter(($container->services['router'] ?? $container->getRouterService()));
        $instance->setRequestParser(($container->privates['Ibexa\\Bundle\\Rest\\RequestParser\\Router'] ?? $container->getRouter2Service()));
        $instance->setRepository(($container->services['ibexa.api.repository'] ?? $container->getIbexa_Api_RepositoryService()));

        return $instance;
    }
}
