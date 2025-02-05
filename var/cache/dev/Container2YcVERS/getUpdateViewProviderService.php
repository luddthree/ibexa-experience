<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getUpdateViewProviderService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\User\View\UserSettings\UpdateViewProvider' shared autowired service.
     *
     * @return \Ibexa\User\View\UserSettings\UpdateViewProvider
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/lib/MVC/Symfony/View/ViewProvider.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/user/src/lib/View/UserSettings/UpdateViewProvider.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/lib/MVC/Symfony/Matcher/MatcherFactoryInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/lib/MVC/Symfony/Matcher/DynamicallyConfiguredMatcherFactoryDecorator.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/lib/MVC/Symfony/Matcher/ConfigurableMatcherFactoryInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/lib/MVC/Symfony/Matcher/ClassNameMatcherFactory.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/bundle/Core/Matcher/ServiceAwareMatcherFactory.php';

        $a = ($container->privates['Ibexa\\Bundle\\Core\\Matcher\\ViewMatcherRegistry'] ?? $container->load('getViewMatcherRegistryService'));

        if (isset($container->privates['Ibexa\\User\\View\\UserSettings\\UpdateViewProvider'])) {
            return $container->privates['Ibexa\\User\\View\\UserSettings\\UpdateViewProvider'];
        }
        $b = ($container->services['ibexa.api.repository'] ?? $container->getIbexa_Api_RepositoryService());

        if (isset($container->privates['Ibexa\\User\\View\\UserSettings\\UpdateViewProvider'])) {
            return $container->privates['Ibexa\\User\\View\\UserSettings\\UpdateViewProvider'];
        }

        return $container->privates['Ibexa\\User\\View\\UserSettings\\UpdateViewProvider'] = new \Ibexa\User\View\UserSettings\UpdateViewProvider(new \Ibexa\Core\MVC\Symfony\Matcher\DynamicallyConfiguredMatcherFactoryDecorator(new \Ibexa\Bundle\Core\Matcher\ServiceAwareMatcherFactory($a, $b, 'Ibexa\\User\\View\\UserSettings\\Matcher'), ($container->services['Ibexa\\Bundle\\Core\\DependencyInjection\\Configuration\\ChainConfigResolver'] ?? $container->getChainConfigResolverService()), 'user_settings_update_view'));
    }
}
