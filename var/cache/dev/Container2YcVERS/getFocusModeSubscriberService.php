<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getFocusModeSubscriberService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\Bundle\SiteContext\EventSubscriber\FocusModeSubscriber' shared autowired service.
     *
     * @return \Ibexa\Bundle\SiteContext\EventSubscriber\FocusModeSubscriber
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/site-context/src/bundle/EventSubscriber/FocusModeSubscriber.php';

        return $container->privates['Ibexa\\Bundle\\SiteContext\\EventSubscriber\\FocusModeSubscriber'] = new \Ibexa\Bundle\SiteContext\EventSubscriber\FocusModeSubscriber(($container->privates['Ibexa\\SiteContext\\SiteContextService'] ?? $container->getSiteContextServiceService()));
    }
}
