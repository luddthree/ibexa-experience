<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getRichTextFieldTypeService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\FieldTypeRichText\Form\Type\RichTextFieldType' shared autowired service.
     *
     * @return \Ibexa\FieldTypeRichText\Form\Type\RichTextFieldType
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/form/FormTypeInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/symfony/form/AbstractType.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/fieldtype-richtext/src/lib/Form/Type/RichTextFieldType.php';

        $a = ($container->services['ibexa.api.service.field_type'] ?? $container->getIbexa_Api_Service_FieldTypeService());

        if (isset($container->privates['Ibexa\\FieldTypeRichText\\Form\\Type\\RichTextFieldType'])) {
            return $container->privates['Ibexa\\FieldTypeRichText\\Form\\Type\\RichTextFieldType'];
        }

        return $container->privates['Ibexa\\FieldTypeRichText\\Form\\Type\\RichTextFieldType'] = new \Ibexa\FieldTypeRichText\Form\Type\RichTextFieldType($a, ($container->privates['Ibexa\\FieldTypeRichText\\RichText\\Converter\\Html5Edit'] ?? $container->getHtml5EditService()));
    }
}
