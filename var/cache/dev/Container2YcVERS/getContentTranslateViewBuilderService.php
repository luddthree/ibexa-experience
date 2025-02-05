<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getContentTranslateViewBuilderService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\AdminUi\View\Builder\ContentTranslateViewBuilder' shared autowired service.
     *
     * @return \Ibexa\AdminUi\View\Builder\ContentTranslateViewBuilder
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/lib/MVC/Symfony/View/Builder/ViewBuilder.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/lib/View/Builder/ContentTranslateViewBuilder.php';

        $a = ($container->services['ibexa.api.repository'] ?? $container->getIbexa_Api_RepositoryService());

        if (isset($container->privates['Ibexa\\AdminUi\\View\\Builder\\ContentTranslateViewBuilder'])) {
            return $container->privates['Ibexa\\AdminUi\\View\\Builder\\ContentTranslateViewBuilder'];
        }
        $b = ($container->privates['Ibexa\\Core\\MVC\\Symfony\\View\\Configurator\\ViewProvider'] ?? $container->load('getViewProviderService'));

        if (isset($container->privates['Ibexa\\AdminUi\\View\\Builder\\ContentTranslateViewBuilder'])) {
            return $container->privates['Ibexa\\AdminUi\\View\\Builder\\ContentTranslateViewBuilder'];
        }
        $c = ($container->privates['Ibexa\\Core\\MVC\\Symfony\\View\\ParametersInjector\\EventDispatcherInjector'] ?? $container->load('getEventDispatcherInjectorService'));

        if (isset($container->privates['Ibexa\\AdminUi\\View\\Builder\\ContentTranslateViewBuilder'])) {
            return $container->privates['Ibexa\\AdminUi\\View\\Builder\\ContentTranslateViewBuilder'];
        }
        $d = ($container->privates['Ibexa\\ContentForms\\Form\\ActionDispatcher\\ContentDispatcher'] ?? $container->load('getContentDispatcherService'));

        if (isset($container->privates['Ibexa\\AdminUi\\View\\Builder\\ContentTranslateViewBuilder'])) {
            return $container->privates['Ibexa\\AdminUi\\View\\Builder\\ContentTranslateViewBuilder'];
        }

        return $container->privates['Ibexa\\AdminUi\\View\\Builder\\ContentTranslateViewBuilder'] = new \Ibexa\AdminUi\View\Builder\ContentTranslateViewBuilder($a, $b, $c, $d, ($container->privates['Ibexa\\Core\\MVC\\Symfony\\Locale\\UserLanguagePreferenceProvider'] ?? $container->getUserLanguagePreferenceProviderService()));
    }
}
