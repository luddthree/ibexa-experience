<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class get_ServiceLocator_Un5F1RYService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private '.service_locator.un5F1RY' shared service.
     *
     * @return \Symfony\Component\DependencyInjection\ServiceLocator
     */
    public static function do($container, $lazyLoad = true)
    {
        return $container->privates['.service_locator.un5F1RY'] = new \Symfony\Component\DependencyInjection\Argument\ServiceLocator($container->getService, [
            'attributeGroup' => ['privates', '.errored..service_locator.un5F1RY.Ibexa\\ProductCatalog\\Local\\Repository\\Values\\AttributeGroup', NULL, 'Cannot autowire service ".service_locator.un5F1RY": it references class "Ibexa\\ProductCatalog\\Local\\Repository\\Values\\AttributeGroup" but no such service exists.'],
        ], [
            'attributeGroup' => 'Ibexa\\ProductCatalog\\Local\\Repository\\Values\\AttributeGroup',
        ]);
    }
}
