<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class get_ServiceLocator_XFNbgd0Service extends App_KernelDevDebugContainer
{
    /**
     * Gets the private '.service_locator.xFNbgd0' shared service.
     *
     * @return \Symfony\Component\DependencyInjection\ServiceLocator
     */
    public static function do($container, $lazyLoad = true)
    {
        return $container->privates['.service_locator.xFNbgd0'] = new \Symfony\Component\DependencyInjection\Argument\ServiceLocator($container->getService, [
            'contentInfo' => ['privates', '.errored..service_locator.xFNbgd0.Ibexa\\Contracts\\Core\\Repository\\Values\\Content\\ContentInfo', NULL, 'Cannot autowire service ".service_locator.xFNbgd0": it references class "Ibexa\\Contracts\\Core\\Repository\\Values\\Content\\ContentInfo" but no such service exists.'],
            'objectStateGroup' => ['privates', '.errored..service_locator.xFNbgd0.Ibexa\\Contracts\\Core\\Repository\\Values\\ObjectState\\ObjectStateGroup', NULL, 'Cannot autowire service ".service_locator.xFNbgd0": it references class "Ibexa\\Contracts\\Core\\Repository\\Values\\ObjectState\\ObjectStateGroup" but no such service exists.'],
        ], [
            'contentInfo' => 'Ibexa\\Contracts\\Core\\Repository\\Values\\Content\\ContentInfo',
            'objectStateGroup' => 'Ibexa\\Contracts\\Core\\Repository\\Values\\ObjectState\\ObjectStateGroup',
        ]);
    }
}
