<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getDateComparisonEngineService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\VersionComparison\Engine\FieldType\DateComparisonEngine' shared autowired service.
     *
     * @return \Ibexa\VersionComparison\Engine\FieldType\DateComparisonEngine
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/version-comparison/src/contracts/Engine/FieldTypeComparisonEngine.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/version-comparison/src/lib/Engine/FieldType/DateComparisonEngine.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/version-comparison/src/lib/Engine/Value/DateTimeComparisonEngine.php';

        return $container->privates['Ibexa\\VersionComparison\\Engine\\FieldType\\DateComparisonEngine'] = new \Ibexa\VersionComparison\Engine\FieldType\DateComparisonEngine(($container->privates['Ibexa\\VersionComparison\\Engine\\Value\\DateTimeComparisonEngine'] ?? ($container->privates['Ibexa\\VersionComparison\\Engine\\Value\\DateTimeComparisonEngine'] = new \Ibexa\VersionComparison\Engine\Value\DateTimeComparisonEngine())));
    }
}
