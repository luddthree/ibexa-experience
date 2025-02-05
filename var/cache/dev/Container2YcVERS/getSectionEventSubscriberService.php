<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getSectionEventSubscriberService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\Core\Search\Common\EventSubscriber\SectionEventSubscriber' shared autowired service.
     *
     * @return \Ibexa\Core\Search\Common\EventSubscriber\SectionEventSubscriber
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/lib/Search/Common/EventSubscriber/AbstractSearchEventSubscriber.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/lib/Search/Common/EventSubscriber/SectionEventSubscriber.php';

        $a = ($container->privates['Ibexa\\Contracts\\Core\\Search\\VersatileHandler'] ?? $container->getVersatileHandlerService());

        if (isset($container->privates['Ibexa\\Core\\Search\\Common\\EventSubscriber\\SectionEventSubscriber'])) {
            return $container->privates['Ibexa\\Core\\Search\\Common\\EventSubscriber\\SectionEventSubscriber'];
        }
        $b = ($container->privates['Ibexa\\Core\\Persistence\\Cache\\Handler'] ?? $container->getHandler2Service());

        if (isset($container->privates['Ibexa\\Core\\Search\\Common\\EventSubscriber\\SectionEventSubscriber'])) {
            return $container->privates['Ibexa\\Core\\Search\\Common\\EventSubscriber\\SectionEventSubscriber'];
        }

        return $container->privates['Ibexa\\Core\\Search\\Common\\EventSubscriber\\SectionEventSubscriber'] = new \Ibexa\Core\Search\Common\EventSubscriber\SectionEventSubscriber($a, $b);
    }
}
