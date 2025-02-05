<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getFormValueTypeService extends App_KernelDevDebugContainer
{
    /**
     * Gets the public 'Ibexa\FormBuilder\Form\Type\FormValueType' shared autowired service.
     *
     * @return \Ibexa\FormBuilder\Form\Type\FormValueType
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/form/FormTypeInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/symfony/form/AbstractType.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/form-builder/src/lib/Form/Type/FormValueType.php';

        return $container->services['Ibexa\\FormBuilder\\Form\\Type\\FormValueType'] = new \Ibexa\FormBuilder\Form\Type\FormValueType(($container->privates['Ibexa\\FormBuilder\\FieldType\\Converter\\FormConverter'] ?? $container->getFormConverterService()));
    }
}
