<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getDirectFragmentRendererService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\Bundle\Core\Fragment\DirectFragmentRenderer' shared service.
     *
     * @return \Ibexa\Bundle\Core\Fragment\DecoratedFragmentRenderer
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/http-kernel/Fragment/FragmentRendererInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/bundle/Core/Fragment/SiteAccessSerializationTrait.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/bundle/Core/Fragment/DecoratedFragmentRenderer.php';

        $a = ($container->privates['Ibexa\\Bundle\\Core\\Fragment\\DirectFragmentRenderer.inner'] ?? $container->load('getDirectFragmentRenderer_InnerService'));

        if (isset($container->privates['Ibexa\\Bundle\\Core\\Fragment\\DirectFragmentRenderer'])) {
            return $container->privates['Ibexa\\Bundle\\Core\\Fragment\\DirectFragmentRenderer'];
        }

        $container->privates['Ibexa\\Bundle\\Core\\Fragment\\DirectFragmentRenderer'] = $instance = new \Ibexa\Bundle\Core\Fragment\DecoratedFragmentRenderer($a);

        $instance->setFragmentPath('/_fragment');
        $instance->setSiteAccess(($container->privates['Ibexa\\Core\\MVC\\Symfony\\SiteAccess'] ?? ($container->privates['Ibexa\\Core\\MVC\\Symfony\\SiteAccess'] = new \Ibexa\Core\MVC\Symfony\SiteAccess('default', 'uninitialized'))));

        return $instance;
    }
}
