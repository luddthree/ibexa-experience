<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getPagerfanta_TwigRuntimeService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'pagerfanta.twig_runtime' shared service.
     *
     * @return \Pagerfanta\Twig\Extension\PagerfantaRuntime
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/twig/twig/src/Extension/RuntimeExtensionInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/pagerfanta/pagerfanta/lib/Twig/Extension/PagerfantaRuntime.php';

        $a = ($container->services['pagerfanta.view_factory'] ?? $container->load('getPagerfanta_ViewFactoryService'));

        if (isset($container->privates['pagerfanta.twig_runtime'])) {
            return $container->privates['pagerfanta.twig_runtime'];
        }
        $b = ($container->services['pagerfanta.route_generator_factory'] ?? $container->load('getPagerfanta_RouteGeneratorFactoryService'));

        if (isset($container->privates['pagerfanta.twig_runtime'])) {
            return $container->privates['pagerfanta.twig_runtime'];
        }

        return $container->privates['pagerfanta.twig_runtime'] = new \Pagerfanta\Twig\Extension\PagerfantaRuntime('default', $a, $b);
    }
}
