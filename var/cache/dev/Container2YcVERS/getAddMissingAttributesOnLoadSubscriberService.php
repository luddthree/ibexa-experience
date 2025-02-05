<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getAddMissingAttributesOnLoadSubscriberService extends App_KernelDevDebugContainer
{
    /**
     * Gets the public 'Ibexa\FieldTypePage\Event\Subscriber\AddMissingAttributesOnLoadSubscriber' shared autowired service.
     *
     * @return \Ibexa\FieldTypePage\Event\Subscriber\AddMissingAttributesOnLoadSubscriber
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/fieldtype-page/src/lib/Event/Subscriber/AddMissingAttributesOnLoadSubscriber.php';

        $a = ($container->services['Ibexa\\FieldTypePage\\FieldType\\Page\\Block\\Definition\\CachedBlockDefinitionFactory'] ?? $container->getCachedBlockDefinitionFactoryService());

        if (isset($container->services['Ibexa\\FieldTypePage\\Event\\Subscriber\\AddMissingAttributesOnLoadSubscriber'])) {
            return $container->services['Ibexa\\FieldTypePage\\Event\\Subscriber\\AddMissingAttributesOnLoadSubscriber'];
        }
        $b = ($container->services['Ibexa\\FieldTypePage\\Serializer\\AttributeSerializationDispatcher'] ?? $container->load('getAttributeSerializationDispatcherService'));

        if (isset($container->services['Ibexa\\FieldTypePage\\Event\\Subscriber\\AddMissingAttributesOnLoadSubscriber'])) {
            return $container->services['Ibexa\\FieldTypePage\\Event\\Subscriber\\AddMissingAttributesOnLoadSubscriber'];
        }

        return $container->services['Ibexa\\FieldTypePage\\Event\\Subscriber\\AddMissingAttributesOnLoadSubscriber'] = new \Ibexa\FieldTypePage\Event\Subscriber\AddMissingAttributesOnLoadSubscriber($a, $b);
    }
}
