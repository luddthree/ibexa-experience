<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getRepositoryEventSubscriberService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\Scheduler\Event\Subscriber\RepositoryEventSubscriber' shared autowired service.
     *
     * @return \Ibexa\Scheduler\Event\Subscriber\RepositoryEventSubscriber
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/scheduler/src/lib/Event/Subscriber/RepositoryEventSubscriber.php';

        $a = ($container->privates['Ibexa\\Scheduler\\ValueObject\\NotificationFactory'] ?? $container->load('getNotificationFactoryService'));

        if (isset($container->privates['Ibexa\\Scheduler\\Event\\Subscriber\\RepositoryEventSubscriber'])) {
            return $container->privates['Ibexa\\Scheduler\\Event\\Subscriber\\RepositoryEventSubscriber'];
        }
        $b = ($container->privates['Ibexa\\Core\\Event\\NotificationService'] ?? $container->getNotificationServiceService());

        if (isset($container->privates['Ibexa\\Scheduler\\Event\\Subscriber\\RepositoryEventSubscriber'])) {
            return $container->privates['Ibexa\\Scheduler\\Event\\Subscriber\\RepositoryEventSubscriber'];
        }
        $c = ($container->privates['Ibexa\\Scheduler\\Repository\\DateBasedPublisherService'] ?? $container->load('getDateBasedPublisherServiceService'));

        if (isset($container->privates['Ibexa\\Scheduler\\Event\\Subscriber\\RepositoryEventSubscriber'])) {
            return $container->privates['Ibexa\\Scheduler\\Event\\Subscriber\\RepositoryEventSubscriber'];
        }

        return $container->privates['Ibexa\\Scheduler\\Event\\Subscriber\\RepositoryEventSubscriber'] = new \Ibexa\Scheduler\Event\Subscriber\RepositoryEventSubscriber(($container->privates['Ibexa\\Scheduler\\Persistence\\Handler'] ?? $container->getHandler39Service()), ($container->services['Symfony\\Contracts\\Translation\\TranslatorInterface'] ?? $container->getTranslatorInterfaceService()), $a, $b, $c);
    }
}
