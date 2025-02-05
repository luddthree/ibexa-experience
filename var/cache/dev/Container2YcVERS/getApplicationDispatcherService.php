<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getApplicationDispatcherService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\CorporateAccount\Form\ActionDispatcher\ApplicationDispatcher' shared service.
     *
     * @return \Ibexa\CorporateAccount\Form\ActionDispatcher\ApplicationDispatcher
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/content-forms/src/lib/Form/ActionDispatcher/ActionDispatcherInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/content-forms/src/lib/Form/ActionDispatcher/AbstractActionDispatcher.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/corporate-account/src/lib/Form/ActionDispatcher/ApplicationDispatcher.php';

        $container->privates['Ibexa\\CorporateAccount\\Form\\ActionDispatcher\\ApplicationDispatcher'] = $instance = new \Ibexa\CorporateAccount\Form\ActionDispatcher\ApplicationDispatcher();

        $instance->setEventDispatcher(($container->services['event_dispatcher'] ?? $container->getEventDispatcherService()));

        return $instance;
    }
}
