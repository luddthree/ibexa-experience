<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getUserEventsSubscriberService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\HttpCache\EventSubscriber\CachePurge\UserEventsSubscriber' shared autowired service.
     *
     * @return \Ibexa\HttpCache\EventSubscriber\CachePurge\UserEventsSubscriber
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/http-cache/src/lib/EventSubscriber/CachePurge/AbstractSubscriber.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/http-cache/src/lib/EventSubscriber/CachePurge/UserEventsSubscriber.php';

        $a = ($container->privates['Ibexa\\Core\\Persistence\\Cache\\LocationHandler'] ?? $container->getLocationHandler2Service());

        if (isset($container->privates['Ibexa\\HttpCache\\EventSubscriber\\CachePurge\\UserEventsSubscriber'])) {
            return $container->privates['Ibexa\\HttpCache\\EventSubscriber\\CachePurge\\UserEventsSubscriber'];
        }
        $b = ($container->privates['Ibexa\\Core\\Persistence\\Cache\\URLHandler'] ?? $container->getURLHandlerService());

        if (isset($container->privates['Ibexa\\HttpCache\\EventSubscriber\\CachePurge\\UserEventsSubscriber'])) {
            return $container->privates['Ibexa\\HttpCache\\EventSubscriber\\CachePurge\\UserEventsSubscriber'];
        }

        return $container->privates['Ibexa\\HttpCache\\EventSubscriber\\CachePurge\\UserEventsSubscriber'] = new \Ibexa\HttpCache\EventSubscriber\CachePurge\UserEventsSubscriber(($container->privates['Ibexa\\HttpCache\\PurgeClient\\RepositoryPrefixDecorator'] ?? $container->getRepositoryPrefixDecoratorService()), $a, $b);
    }
}
