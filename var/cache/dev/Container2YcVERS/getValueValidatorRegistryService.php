<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getValueValidatorRegistryService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\ProductCatalog\Local\Repository\Attribute\ValueValidatorRegistry' shared autowired service.
     *
     * @return \Ibexa\ProductCatalog\Local\Repository\Attribute\ValueValidatorRegistry
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/product-catalog/src/lib/Local/Repository/Attribute/ValueValidatorRegistryInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/product-catalog/src/lib/Local/Repository/Attribute/ValueValidatorRegistry.php';

        return $container->privates['Ibexa\\ProductCatalog\\Local\\Repository\\Attribute\\ValueValidatorRegistry'] = new \Ibexa\ProductCatalog\Local\Repository\Attribute\ValueValidatorRegistry(new RewindableGenerator(function () use ($container) {
            yield 'color' => ($container->privates['Ibexa\\ProductCatalog\\Local\\Repository\\Attribute\\ColorValueValidator'] ?? ($container->privates['Ibexa\\ProductCatalog\\Local\\Repository\\Attribute\\ColorValueValidator'] = new \Ibexa\ProductCatalog\Local\Repository\Attribute\ColorValueValidator()));
            yield 'float' => ($container->privates['ibexa.product_catalog.attribute.value_validator.float'] ?? ($container->privates['ibexa.product_catalog.attribute.value_validator.float'] = new \Ibexa\ProductCatalog\Local\Repository\Attribute\NumericValueValidator()));
            yield 'integer' => ($container->privates['ibexa.product_catalog.attribute.value_validator.integer'] ?? ($container->privates['ibexa.product_catalog.attribute.value_validator.integer'] = new \Ibexa\ProductCatalog\Local\Repository\Attribute\NumericValueValidator()));
            yield 'selection' => ($container->privates['Ibexa\\ProductCatalog\\Local\\Repository\\Attribute\\SelectionValueValidator'] ?? ($container->privates['Ibexa\\ProductCatalog\\Local\\Repository\\Attribute\\SelectionValueValidator'] = new \Ibexa\ProductCatalog\Local\Repository\Attribute\SelectionValueValidator()));
        }, 4));
    }
}
