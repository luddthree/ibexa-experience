<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getAttributeLocationTypeService extends App_KernelDevDebugContainer
{
    /**
     * Gets the public 'Ibexa\FormBuilder\Form\Type\FieldAttribute\AttributeLocationType' shared autowired service.
     *
     * @return \Ibexa\FormBuilder\Form\Type\FieldAttribute\AttributeLocationType
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/form/FormTypeInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/symfony/form/AbstractType.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/form-builder/src/lib/Form/Type/FieldAttribute/AttributeLocationType.php';

        $a = ($container->services['ibexa.api.service.location'] ?? $container->getIbexa_Api_Service_LocationService());

        if (isset($container->services['Ibexa\\FormBuilder\\Form\\Type\\FieldAttribute\\AttributeLocationType'])) {
            return $container->services['Ibexa\\FormBuilder\\Form\\Type\\FieldAttribute\\AttributeLocationType'];
        }

        return $container->services['Ibexa\\FormBuilder\\Form\\Type\\FieldAttribute\\AttributeLocationType'] = new \Ibexa\FormBuilder\Form\Type\FieldAttribute\AttributeLocationType($a);
    }
}
