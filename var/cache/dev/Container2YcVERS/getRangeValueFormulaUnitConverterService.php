<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getRangeValueFormulaUnitConverterService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\Measurement\UnitConverter\RangeValueFormulaUnitConverter' shared autowired service.
     *
     * @return \Ibexa\Measurement\UnitConverter\RangeValueFormulaUnitConverter
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/measurement/src/contracts/Converter/UnitConverterInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/measurement/src/lib/UnitConverter/AbstractFormulaUnitConverter.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/measurement/src/lib/UnitConverter/RangeValueFormulaUnitConverter.php';

        $a = ($container->privates['Ibexa\\Measurement\\MeasurementService'] ?? $container->getMeasurementServiceService());

        if (isset($container->privates['Ibexa\\Measurement\\UnitConverter\\RangeValueFormulaUnitConverter'])) {
            return $container->privates['Ibexa\\Measurement\\UnitConverter\\RangeValueFormulaUnitConverter'];
        }

        return $container->privates['Ibexa\\Measurement\\UnitConverter\\RangeValueFormulaUnitConverter'] = new \Ibexa\Measurement\UnitConverter\RangeValueFormulaUnitConverter($a, ($container->privates['ibexa.measurement.unit_converter.formula_unit_converter.expression_engine'] ?? ($container->privates['ibexa.measurement.unit_converter.formula_unit_converter.expression_engine'] = new \Symfony\Component\ExpressionLanguage\ExpressionLanguage(NULL))), $container->parameters['ibexa.measurement.value.converter.formulas']);
    }
}
