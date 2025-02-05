<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getItemResolverService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\GraphQL\Resolver\ItemResolver' shared autowired service.
     *
     * @return \Ibexa\GraphQL\Resolver\ItemResolver
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/graphql/src/lib/Resolver/ItemResolver.php';

        $a = ($container->services['overblog_graphql.type_resolver'] ?? $container->load('getOverblogGraphql_TypeResolverService'));

        if (isset($container->privates['Ibexa\\GraphQL\\Resolver\\ItemResolver'])) {
            return $container->privates['Ibexa\\GraphQL\\Resolver\\ItemResolver'];
        }

        return $container->privates['Ibexa\\GraphQL\\Resolver\\ItemResolver'] = new \Ibexa\GraphQL\Resolver\ItemResolver($a, ($container->privates['Ibexa\\GraphQL\\InputMapper\\SearchQueryMapper'] ?? $container->load('getSearchQueryMapperService')), ($container->privates['Ibexa\\GraphQL\\DataLoader\\CachedContentLoader'] ?? $container->load('getCachedContentLoaderService')), ($container->privates['Ibexa\\GraphQL\\DataLoader\\CachedContentTypeLoader'] ?? $container->load('getCachedContentTypeLoaderService')), ($container->privates['Ibexa\\GraphQL\\DataLoader\\SearchLocationLoader'] ?? $container->load('getSearchLocationLoaderService')), ($container->privates['Ibexa\\GraphQL\\ItemFactory\\CurrentSite'] ?? $container->load('getCurrentSiteService')));
    }
}
