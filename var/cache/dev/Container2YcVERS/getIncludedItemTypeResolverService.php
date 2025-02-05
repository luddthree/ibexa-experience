<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getIncludedItemTypeResolverService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\Personalization\Config\ItemType\IncludedItemTypeResolver' shared autowired service.
     *
     * @return \Ibexa\Personalization\Config\ItemType\IncludedItemTypeResolver
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/personalization/src/lib/Config/ItemType/IncludedItemTypeResolverInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/personalization/src/lib/Config/ItemType/IncludedItemTypeResolver.php';

        $a = ($container->privates['Ibexa\\AdminUi\\Siteaccess\\SiteaccessResolver'] ?? $container->getSiteaccessResolverService());

        if (isset($container->privates['Ibexa\\Personalization\\Config\\ItemType\\IncludedItemTypeResolver'])) {
            return $container->privates['Ibexa\\Personalization\\Config\\ItemType\\IncludedItemTypeResolver'];
        }
        $b = ($container->privates['monolog.logger.ibexa.personalization'] ?? $container->getMonolog_Logger_Ibexa_PersonalizationService());

        $container->privates['Ibexa\\Personalization\\Config\\ItemType\\IncludedItemTypeResolver'] = $instance = new \Ibexa\Personalization\Config\ItemType\IncludedItemTypeResolver(($container->services['Ibexa\\Bundle\\Core\\DependencyInjection\\Configuration\\ChainConfigResolver'] ?? $container->getChainConfigResolverService()), $a, $b);

        $instance->setLogger($b);

        return $instance;
    }
}
