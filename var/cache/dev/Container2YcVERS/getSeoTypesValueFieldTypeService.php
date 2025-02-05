<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getSeoTypesValueFieldTypeService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\Seo\Form\Type\FieldType\SeoTypesValueFieldType' shared autowired service.
     *
     * @return \Ibexa\Seo\Form\Type\FieldType\SeoTypesValueFieldType
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/form/FormTypeInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/symfony/form/AbstractType.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/seo/src/lib/Form/Type/FieldType/SeoTypesValueFieldType.php';

        return $container->privates['Ibexa\\Seo\\Form\\Type\\FieldType\\SeoTypesValueFieldType'] = new \Ibexa\Seo\Form\Type\FieldType\SeoTypesValueFieldType();
    }
}
