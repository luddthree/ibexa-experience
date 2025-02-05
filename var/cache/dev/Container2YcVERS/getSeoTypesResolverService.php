<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getSeoTypesResolverService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\Seo\Resolver\SeoTypesResolver' shared autowired service.
     *
     * @return \Ibexa\Seo\Resolver\SeoTypesResolver
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/seo/src/contracts/Resolver/SeoTypesResolverInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/seo/src/lib/Resolver/SeoTypesResolver.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/seo/src/contracts/ConfigResolver/SeoTypesInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/seo/src/lib/ConfigResolver/SeoTypes.php';

        return $container->privates['Ibexa\\Seo\\Resolver\\SeoTypesResolver'] = new \Ibexa\Seo\Resolver\SeoTypesResolver(new \Ibexa\Seo\ConfigResolver\SeoTypes(($container->services['Ibexa\\Bundle\\Core\\DependencyInjection\\Configuration\\ChainConfigResolver'] ?? $container->getChainConfigResolverService())), $container->parameters['ibexa.seo.types']);
    }
}
