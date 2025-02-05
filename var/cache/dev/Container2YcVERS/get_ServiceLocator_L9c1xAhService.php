<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class get_ServiceLocator_L9c1xAhService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private '.service_locator.L9c1xAh' shared service.
     *
     * @return \Symfony\Component\DependencyInjection\ServiceLocator
     */
    public static function do($container, $lazyLoad = true)
    {
        return $container->privates['.service_locator.L9c1xAh'] = new \Symfony\Component\DependencyInjection\Argument\ServiceLocator($container->getService, [
            'catalog' => ['privates', '.errored..service_locator.L9c1xAh.Ibexa\\ProductCatalog\\Local\\Repository\\Values\\Catalog\\Catalog', NULL, 'Cannot autowire service ".service_locator.L9c1xAh": it references class "Ibexa\\ProductCatalog\\Local\\Repository\\Values\\Catalog\\Catalog" but no such service exists.'],
        ], [
            'catalog' => 'Ibexa\\ProductCatalog\\Local\\Repository\\Values\\Catalog\\Catalog',
        ]);
    }
}
