<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getIbexa_ProductCatalog_AttributeType_FloatService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'ibexa.product_catalog.attribute_type.float' shared autowired service.
     *
     * @return \Ibexa\ProductCatalog\Local\Repository\Attribute\AttributeType
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/product-catalog/src/contracts/Values/AttributeTypeInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/product-catalog/src/lib/Local/Repository/Attribute/AttributeType.php';

        return $container->privates['ibexa.product_catalog.attribute_type.float'] = new \Ibexa\ProductCatalog\Local\Repository\Attribute\AttributeType(($container->services['Symfony\\Contracts\\Translation\\TranslatorInterface'] ?? $container->getTranslatorInterfaceService()), 'float');
    }
}
