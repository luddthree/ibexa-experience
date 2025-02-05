<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getBlockAttributeStorageValueResolverService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\FieldTypePage\GraphQL\Resolver\BlockAttributeStorageValueResolver' shared autowired service.
     *
     * @return \Ibexa\FieldTypePage\GraphQL\Resolver\BlockAttributeStorageValueResolver
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/fieldtype-page/src/lib/GraphQL/Resolver/BlockAttributeStorageValueResolver.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/fieldtype-page/src/contracts/FieldType/Page/Block/Attribute/ValueConverter/ValueConverterInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/fieldtype-page/src/lib/FieldType/Page/Block/Attribute/ValueConverter/Multiple.php';

        return $container->privates['Ibexa\\FieldTypePage\\GraphQL\\Resolver\\BlockAttributeStorageValueResolver'] = new \Ibexa\FieldTypePage\GraphQL\Resolver\BlockAttributeStorageValueResolver(($container->services['Ibexa\\FieldTypePage\\Serializer\\AttributeSerializationDispatcher'] ?? $container->load('getAttributeSerializationDispatcherService')), ($container->services['Ibexa\\FieldTypePage\\FieldType\\Page\\Block\\Attribute\\ValueConverter\\LocationList'] ?? $container->load('getLocationListService')), ($container->services['Ibexa\\FieldTypePage\\FieldType\\Page\\Block\\Attribute\\ValueConverter\\ContentTypeList'] ?? $container->load('getContentTypeListService')), ($container->services['Ibexa\\FieldTypePage\\FieldType\\Page\\Block\\Attribute\\ValueConverter\\Multiple'] ?? ($container->services['Ibexa\\FieldTypePage\\FieldType\\Page\\Block\\Attribute\\ValueConverter\\Multiple'] = new \Ibexa\FieldTypePage\FieldType\Page\Block\Attribute\ValueConverter\Multiple())), ($container->privates['Ibexa\\GraphQL\\DataLoader\\CachedContentLoader'] ?? $container->load('getCachedContentLoaderService')), ($container->privates['Ibexa\\GraphQL\\ItemFactory\\CurrentSite'] ?? $container->load('getCurrentSiteService')));
    }
}
