<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getAttributeTypeInterfaceService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private '.errored..service_locator.hkOJzVu.Ibexa\Contracts\ProductCatalog\Values\AttributeTypeInterface' shared service.
     *
     * @return \Ibexa\Contracts\ProductCatalog\Values\AttributeTypeInterface
     */
    public static function do($container, $lazyLoad = true)
    {
        $container->throw('Cannot autowire service ".service_locator.hkOJzVu": it references interface "Ibexa\\Contracts\\ProductCatalog\\Values\\AttributeTypeInterface" but no such service exists. You should maybe alias this interface to one of these existing services: "ibexa.product_catalog.attribute_type.measurement", "ibexa.measurement.product_catalog.attribute_type.measurement_range", "ibexa.measurement.product_catalog.attribute_type.measurement_single", "ibexa.product_catalog.attribute_type.checkbox", "ibexa.product_catalog.attribute_type.color", "ibexa.product_catalog.attribute_type.float", "ibexa.product_catalog.attribute_type.integer", "ibexa.product_catalog.attribute_type.selection".');
    }
}
