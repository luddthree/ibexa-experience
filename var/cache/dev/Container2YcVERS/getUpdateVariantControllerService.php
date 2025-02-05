<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getUpdateVariantControllerService extends App_KernelDevDebugContainer
{
    /**
     * Gets the public 'Ibexa\Bundle\ProductCatalog\Controller\Product\UpdateVariantController' shared autowired service.
     *
     * @return \Ibexa\Bundle\ProductCatalog\Controller\Product\UpdateVariantController
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/framework-bundle/Controller/AbstractController.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/contracts/Controller/Controller.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/product-catalog/src/bundle/Controller/Product/Controller.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/product-catalog/src/bundle/Controller/Product/UpdateVariantController.php';

        $container->services['Ibexa\\Bundle\\ProductCatalog\\Controller\\Product\\UpdateVariantController'] = $instance = new \Ibexa\Bundle\ProductCatalog\Controller\Product\UpdateVariantController(($container->privates['Ibexa\\ProductCatalog\\Local\\Repository\\Event\\ProductService'] ?? $container->load('getProductServiceService')), ($container->privates['Ibexa\\AdminUi\\Form\\SubmitHandler'] ?? $container->load('getSubmitHandlerService')));

        $instance->setContainer(($container->privates['.service_locator.mx0UMmY'] ?? $container->load('get_ServiceLocator_Mx0UMmYService'))->withContext('Ibexa\\Bundle\\ProductCatalog\\Controller\\Product\\UpdateVariantController', $container));

        return $instance;
    }
}
