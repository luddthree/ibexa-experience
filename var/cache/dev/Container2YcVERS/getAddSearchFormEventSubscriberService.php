<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getAddSearchFormEventSubscriberService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\Connector\Dam\Event\AddSearchFormEventSubscriber' shared autowired service.
     *
     * @return \Ibexa\Connector\Dam\Event\AddSearchFormEventSubscriber
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/connector-dam/src/lib/Event/AddSearchFormEventSubscriber.php';

        $a = ($container->services['.container.private.form.factory'] ?? $container->get_Container_Private_Form_FactoryService());

        if (isset($container->privates['Ibexa\\Connector\\Dam\\Event\\AddSearchFormEventSubscriber'])) {
            return $container->privates['Ibexa\\Connector\\Dam\\Event\\AddSearchFormEventSubscriber'];
        }

        return $container->privates['Ibexa\\Connector\\Dam\\Event\\AddSearchFormEventSubscriber'] = new \Ibexa\Connector\Dam\Event\AddSearchFormEventSubscriber($a, 'Symfony\\Component\\Form\\Extension\\Core\\Type\\SearchType');
    }
}
