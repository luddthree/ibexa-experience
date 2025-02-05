<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getContentTranslateViewFilterService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\AdminUi\View\Filter\ContentTranslateViewFilter' shared autowired service.
     *
     * @return \Ibexa\AdminUi\View\Filter\ContentTranslateViewFilter
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/lib/View/Filter/ContentTranslateViewFilter.php';

        $a = ($container->services['ibexa.api.service.content'] ?? $container->getIbexa_Api_Service_ContentService());

        if (isset($container->privates['Ibexa\\AdminUi\\View\\Filter\\ContentTranslateViewFilter'])) {
            return $container->privates['Ibexa\\AdminUi\\View\\Filter\\ContentTranslateViewFilter'];
        }
        $b = ($container->services['ibexa.api.service.content_type'] ?? $container->getIbexa_Api_Service_ContentTypeService());

        if (isset($container->privates['Ibexa\\AdminUi\\View\\Filter\\ContentTranslateViewFilter'])) {
            return $container->privates['Ibexa\\AdminUi\\View\\Filter\\ContentTranslateViewFilter'];
        }
        $c = ($container->services['.container.private.form.factory'] ?? $container->get_Container_Private_Form_FactoryService());

        if (isset($container->privates['Ibexa\\AdminUi\\View\\Filter\\ContentTranslateViewFilter'])) {
            return $container->privates['Ibexa\\AdminUi\\View\\Filter\\ContentTranslateViewFilter'];
        }

        return $container->privates['Ibexa\\AdminUi\\View\\Filter\\ContentTranslateViewFilter'] = new \Ibexa\AdminUi\View\Filter\ContentTranslateViewFilter($a, ($container->services['ibexa.api.service.language'] ?? $container->getIbexa_Api_Service_LanguageService()), $b, $c, ($container->privates['Ibexa\\Core\\MVC\\Symfony\\Locale\\UserLanguagePreferenceProvider'] ?? $container->getUserLanguagePreferenceProviderService()));
    }
}
