<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getFieldTypeToContentTypeStrategyService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\FieldTypeMatrix\FieldType\Mapper\FieldTypeToContentTypeStrategy' shared autowired service.
     *
     * @return \Ibexa\FieldTypeMatrix\FieldType\Mapper\FieldTypeToContentTypeStrategy
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/fieldtype-matrix/src/lib/FieldType/Mapper/FieldTypeToContentTypeStrategyInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/fieldtype-matrix/src/lib/FieldType/Mapper/FieldTypeToContentTypeStrategy.php';

        return $container->privates['Ibexa\\FieldTypeMatrix\\FieldType\\Mapper\\FieldTypeToContentTypeStrategy'] = new \Ibexa\FieldTypeMatrix\FieldType\Mapper\FieldTypeToContentTypeStrategy(($container->services['ibexa.api.service.content_type'] ?? $container->getIbexa_Api_Service_ContentTypeService()));
    }
}
