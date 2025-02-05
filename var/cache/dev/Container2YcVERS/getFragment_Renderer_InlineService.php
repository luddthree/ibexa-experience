<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getFragment_Renderer_InlineService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'fragment.renderer.inline' shared service.
     *
     * @return \Ibexa\Bundle\Core\Fragment\InlineFragmentRenderer
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/http-kernel/Fragment/FragmentRendererInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/symfony/http-kernel/Fragment/RoutableFragmentRenderer.php';
        include_once \dirname(__DIR__, 4).'/vendor/symfony/http-kernel/Fragment/InlineFragmentRenderer.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/bundle/Core/Fragment/SiteAccessSerializationTrait.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/bundle/Core/Fragment/InlineFragmentRenderer.php';

        $a = ($container->privates['fragment.renderer.inline.inner'] ?? $container->load('getFragment_Renderer_Inline_InnerService'));

        if (isset($container->privates['fragment.renderer.inline'])) {
            return $container->privates['fragment.renderer.inline'];
        }

        $container->privates['fragment.renderer.inline'] = $instance = new \Ibexa\Bundle\Core\Fragment\InlineFragmentRenderer($a);

        $instance->setFragmentPath('/_fragment');
        $instance->setSiteAccess(($container->privates['Ibexa\\Core\\MVC\\Symfony\\SiteAccess'] ?? ($container->privates['Ibexa\\Core\\MVC\\Symfony\\SiteAccess'] = new \Ibexa\Core\MVC\Symfony\SiteAccess('default', 'uninitialized'))));

        return $instance;
    }
}
