<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getOptionsFormMapperRegistryService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\Bundle\ProductCatalog\Form\Attribute\OptionsFormMapperRegistry' shared autowired service.
     *
     * @return \Ibexa\Bundle\ProductCatalog\Form\Attribute\OptionsFormMapperRegistry
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/product-catalog/src/bundle/Form/Attribute/OptionsFormMapperRegistryInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/product-catalog/src/bundle/Form/Attribute/OptionsFormMapperRegistry.php';

        return $container->privates['Ibexa\\Bundle\\ProductCatalog\\Form\\Attribute\\OptionsFormMapperRegistry'] = new \Ibexa\Bundle\ProductCatalog\Form\Attribute\OptionsFormMapperRegistry(new RewindableGenerator(function () use ($container) {
            yield 'measurement' => ($container->privates['Ibexa\\Measurement\\ProductCatalog\\Form\\Attribute\\Mapper\\MeasurementFormMapper'] ?? ($container->privates['Ibexa\\Measurement\\ProductCatalog\\Form\\Attribute\\Mapper\\MeasurementFormMapper'] = new \Ibexa\Measurement\ProductCatalog\Form\Attribute\Mapper\MeasurementFormMapper('Ibexa\\Measurement\\ProductCatalog\\Form\\Attribute\\Type\\AttributeDefinitionMeasurementType')));
            yield 'measurement_single' => ($container->privates['ibexa.measurement.product_catalog.form.attribute.mapper.simple_measurement_form_mapper'] ?? ($container->privates['ibexa.measurement.product_catalog.form.attribute.mapper.simple_measurement_form_mapper'] = new \Ibexa\Measurement\ProductCatalog\Form\Attribute\Mapper\MeasurementFormMapper('Ibexa\\Measurement\\ProductCatalog\\Form\\Attribute\\Type\\SimpleAttributeDefinitionMeasurementType')));
            yield 'measurement_range' => ($container->privates['ibexa.measurement.product_catalog.form.attribute.mapper.range_measurement_form_mapper'] ?? ($container->privates['ibexa.measurement.product_catalog.form.attribute.mapper.range_measurement_form_mapper'] = new \Ibexa\Measurement\ProductCatalog\Form\Attribute\Mapper\MeasurementFormMapper('Ibexa\\Measurement\\ProductCatalog\\Form\\Attribute\\Type\\RangeAttributeDefinitionMeasurementType')));
            yield 'float' => ($container->privates['ibexa.product_catalog.attribute.float.form_mapper.options'] ?? ($container->privates['ibexa.product_catalog.attribute.float.form_mapper.options'] = new \Ibexa\Bundle\ProductCatalog\Form\Attribute\NumericOptionsFormMapper('Ibexa\\Bundle\\ProductCatalog\\Form\\Type\\FloatAttributeOptionsType')));
            yield 'integer' => ($container->privates['ibexa.product_catalog.attribute.integer.form_mapper.options'] ?? ($container->privates['ibexa.product_catalog.attribute.integer.form_mapper.options'] = new \Ibexa\Bundle\ProductCatalog\Form\Attribute\NumericOptionsFormMapper('Ibexa\\Bundle\\ProductCatalog\\Form\\Type\\IntegerAttributeOptionsType')));
            yield 'selection' => ($container->privates['Ibexa\\Bundle\\ProductCatalog\\Form\\Attribute\\SelectionOptionsFormMapper'] ?? ($container->privates['Ibexa\\Bundle\\ProductCatalog\\Form\\Attribute\\SelectionOptionsFormMapper'] = new \Ibexa\Bundle\ProductCatalog\Form\Attribute\SelectionOptionsFormMapper()));
        }, 6));
    }
}
