<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getContentDownloadRouteReferenceListener2Service extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\Bundle\Core\EventListener\ContentDownloadRouteReferenceListener' shared service.
     *
     * @return \Ibexa\Bundle\Core\EventListener\ContentDownloadRouteReferenceListener
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/bundle/Core/EventListener/ContentDownloadRouteReferenceListener.php';

        return $container->privates['Ibexa\\Bundle\\Core\\EventListener\\ContentDownloadRouteReferenceListener'] = new \Ibexa\Bundle\Core\EventListener\ContentDownloadRouteReferenceListener(($container->privates['Ibexa\\Core\\Helper\\TranslationHelper'] ?? $container->getTranslationHelperService()));
    }
}
