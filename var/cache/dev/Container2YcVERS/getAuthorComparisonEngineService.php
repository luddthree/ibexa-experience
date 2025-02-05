<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getAuthorComparisonEngineService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\VersionComparison\Engine\FieldType\AuthorComparisonEngine' shared autowired service.
     *
     * @return \Ibexa\VersionComparison\Engine\FieldType\AuthorComparisonEngine
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/version-comparison/src/contracts/Engine/FieldTypeComparisonEngine.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/version-comparison/src/lib/Engine/FieldType/AuthorComparisonEngine.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/version-comparison/src/lib/Engine/Value/StringComparisonEngine.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/version-comparison/src/lib/Engine/Value/CollectionComparisonEngine.php';

        return $container->privates['Ibexa\\VersionComparison\\Engine\\FieldType\\AuthorComparisonEngine'] = new \Ibexa\VersionComparison\Engine\FieldType\AuthorComparisonEngine(($container->privates['Ibexa\\VersionComparison\\Engine\\Value\\StringComparisonEngine'] ?? ($container->privates['Ibexa\\VersionComparison\\Engine\\Value\\StringComparisonEngine'] = new \Ibexa\VersionComparison\Engine\Value\StringComparisonEngine())), ($container->privates['Ibexa\\VersionComparison\\Engine\\Value\\CollectionComparisonEngine'] ?? ($container->privates['Ibexa\\VersionComparison\\Engine\\Value\\CollectionComparisonEngine'] = new \Ibexa\VersionComparison\Engine\Value\CollectionComparisonEngine())));
    }
}
