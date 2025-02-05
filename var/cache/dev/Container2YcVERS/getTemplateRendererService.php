<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getTemplateRendererService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\Core\MVC\Symfony\View\Renderer\TemplateRenderer' shared service.
     *
     * @return \Ibexa\Core\MVC\Symfony\View\Renderer\TemplateRenderer
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/lib/MVC/Symfony/View/Renderer.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/lib/MVC/Symfony/View/Renderer/TemplateRenderer.php';

        $a = ($container->services['.container.private.twig'] ?? $container->get_Container_Private_TwigService());

        if (isset($container->privates['Ibexa\\Core\\MVC\\Symfony\\View\\Renderer\\TemplateRenderer'])) {
            return $container->privates['Ibexa\\Core\\MVC\\Symfony\\View\\Renderer\\TemplateRenderer'];
        }
        $b = ($container->services['event_dispatcher'] ?? $container->getEventDispatcherService());

        if (isset($container->privates['Ibexa\\Core\\MVC\\Symfony\\View\\Renderer\\TemplateRenderer'])) {
            return $container->privates['Ibexa\\Core\\MVC\\Symfony\\View\\Renderer\\TemplateRenderer'];
        }

        return $container->privates['Ibexa\\Core\\MVC\\Symfony\\View\\Renderer\\TemplateRenderer'] = new \Ibexa\Core\MVC\Symfony\View\Renderer\TemplateRenderer($a, $b);
    }
}
