<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class get_ServiceLocator_HuKsUB6Service extends App_KernelDevDebugContainer
{
    /**
     * Gets the private '.service_locator.huKsUB6' shared service.
     *
     * @return \Symfony\Component\DependencyInjection\ServiceLocator
     */
    public static function do($container, $lazyLoad = true)
    {
        return $container->privates['.service_locator.huKsUB6'] = new \Symfony\Component\DependencyInjection\Argument\ServiceLocator($container->getService, [
            'contentType' => ['privates', '.errored..service_locator.huKsUB6.Ibexa\\Contracts\\Core\\Repository\\Values\\ContentType\\ContentType', NULL, 'Cannot autowire service ".service_locator.huKsUB6": it references class "Ibexa\\Contracts\\Core\\Repository\\Values\\ContentType\\ContentType" but no such service exists.'],
            'group' => ['privates', '.errored..service_locator.huKsUB6.Ibexa\\Contracts\\Core\\Repository\\Values\\ContentType\\ContentTypeGroup', NULL, 'Cannot autowire service ".service_locator.huKsUB6": it references class "Ibexa\\Contracts\\Core\\Repository\\Values\\ContentType\\ContentTypeGroup" but no such service exists.'],
        ], [
            'contentType' => 'Ibexa\\Contracts\\Core\\Repository\\Values\\ContentType\\ContentType',
            'group' => 'Ibexa\\Contracts\\Core\\Repository\\Values\\ContentType\\ContentTypeGroup',
        ]);
    }
}
