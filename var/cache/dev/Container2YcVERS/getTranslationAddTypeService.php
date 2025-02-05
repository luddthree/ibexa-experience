<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getTranslationAddTypeService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\AdminUi\Form\Type\ContentType\Translation\TranslationAddType' shared autowired service.
     *
     * @return \Ibexa\AdminUi\Form\Type\ContentType\Translation\TranslationAddType
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/form/FormTypeInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/symfony/form/AbstractType.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/lib/Form/Type/ContentType/Translation/TranslationAddType.php';

        $a = ($container->services['ibexa.api.service.content_type'] ?? $container->getIbexa_Api_Service_ContentTypeService());

        if (isset($container->privates['Ibexa\\AdminUi\\Form\\Type\\ContentType\\Translation\\TranslationAddType'])) {
            return $container->privates['Ibexa\\AdminUi\\Form\\Type\\ContentType\\Translation\\TranslationAddType'];
        }

        return $container->privates['Ibexa\\AdminUi\\Form\\Type\\ContentType\\Translation\\TranslationAddType'] = new \Ibexa\AdminUi\Form\Type\ContentType\Translation\TranslationAddType(($container->services['ibexa.api.service.language'] ?? $container->getIbexa_Api_Service_LanguageService()), $a);
    }
}
