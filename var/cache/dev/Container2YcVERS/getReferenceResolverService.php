<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getReferenceResolverService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\Bundle\Migration\Serializer\Denormalizer\Template\ReferenceResolver' shared autowired service.
     *
     * @return \Ibexa\Bundle\Migration\Serializer\Denormalizer\Template\ReferenceResolver
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/migrations/src/bundle/Serializer/Denormalizer/Template/ReferenceResolver.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/migrations/src/lib/Reference/CollectorInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/migrations/src/lib/Reference/Collector.php';

        return $container->privates['Ibexa\\Bundle\\Migration\\Serializer\\Denormalizer\\Template\\ReferenceResolver'] = new \Ibexa\Bundle\Migration\Serializer\Denormalizer\Template\ReferenceResolver(($container->privates['Ibexa\\Migration\\Reference\\Collector'] ?? ($container->privates['Ibexa\\Migration\\Reference\\Collector'] = new \Ibexa\Migration\Reference\Collector())));
    }
}
