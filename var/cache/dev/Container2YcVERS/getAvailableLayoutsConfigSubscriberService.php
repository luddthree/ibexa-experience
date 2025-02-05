<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getAvailableLayoutsConfigSubscriberService extends App_KernelDevDebugContainer
{
    /**
     * Gets the public 'Ibexa\Dashboard\EventSubscriber\AvailableLayoutsConfigSubscriber' shared autowired service.
     *
     * @return \Ibexa\Dashboard\EventSubscriber\AvailableLayoutsConfigSubscriber
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/dashboard/src/lib/EventSubscriber/AvailableLayoutsConfigSubscriber.php';

        return $container->services['Ibexa\\Dashboard\\EventSubscriber\\AvailableLayoutsConfigSubscriber'] = new \Ibexa\Dashboard\EventSubscriber\AvailableLayoutsConfigSubscriber(($container->services['Ibexa\\Bundle\\Core\\DependencyInjection\\Configuration\\ChainConfigResolver'] ?? $container->getChainConfigResolverService()));
    }
}
