<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getRadioFieldMapperService extends App_KernelDevDebugContainer
{
    /**
     * Gets the public 'Ibexa\FormBuilder\FieldType\Field\Mapper\RadioFieldMapper' shared autowired service.
     *
     * @return \Ibexa\FormBuilder\FieldType\Field\Mapper\RadioFieldMapper
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/form-builder/src/contracts/FieldType/Field/FieldMapperInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/form-builder/src/lib/FieldType/Field/Mapper/GenericFieldMapper.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/form-builder/src/lib/FieldType/Field/Mapper/RadioFieldMapper.php';

        return $container->services['Ibexa\\FormBuilder\\FieldType\\Field\\Mapper\\RadioFieldMapper'] = new \Ibexa\FormBuilder\FieldType\Field\Mapper\RadioFieldMapper('radio', 'Ibexa\\FormBuilder\\Form\\Type\\Field\\RadioFieldType');
    }
}
