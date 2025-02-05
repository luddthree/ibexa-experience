<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getIbexa_Measurement_UnitConverter_Custom_SimpleValueFormulaService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'ibexa.measurement.unit_converter.custom.simple_value_formula' shared autowired service.
     *
     * @return \Ibexa\Measurement\UnitConverter\SimpleValueFormulaUnitConverter
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/measurement/src/contracts/Converter/UnitConverterInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/measurement/src/lib/UnitConverter/AbstractFormulaUnitConverter.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/measurement/src/lib/UnitConverter/SimpleValueFormulaUnitConverter.php';

        $a = ($container->privates['Ibexa\\Measurement\\MeasurementService'] ?? $container->getMeasurementServiceService());

        if (isset($container->privates['ibexa.measurement.unit_converter.custom.simple_value_formula'])) {
            return $container->privates['ibexa.measurement.unit_converter.custom.simple_value_formula'];
        }

        return $container->privates['ibexa.measurement.unit_converter.custom.simple_value_formula'] = new \Ibexa\Measurement\UnitConverter\SimpleValueFormulaUnitConverter($a, ($container->privates['ibexa.measurement.unit_converter.formula_unit_converter.expression_engine'] ?? ($container->privates['ibexa.measurement.unit_converter.formula_unit_converter.expression_engine'] = new \Symfony\Component\ExpressionLanguage\ExpressionLanguage(NULL))), []);
    }
}
