<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getProxyCacheWarmer4Service extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\Workflow\Proxy\ProxyCacheWarmer' shared autowired service.
     *
     * @return \Ibexa\Workflow\Proxy\ProxyCacheWarmer
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/http-kernel/CacheWarmer/CacheWarmerInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/workflow/src/lib/Proxy/ProxyCacheWarmer.php';

        return $container->privates['Ibexa\\Workflow\\Proxy\\ProxyCacheWarmer'] = new \Ibexa\Workflow\Proxy\ProxyCacheWarmer(($container->privates['Ibexa\\Core\\Repository\\ProxyFactory\\ProxyGenerator'] ?? ($container->privates['Ibexa\\Core\\Repository\\ProxyFactory\\ProxyGenerator'] = new \Ibexa\Core\Repository\ProxyFactory\ProxyGenerator(($container->targetDir.''.'/repository/proxy')))));
    }
}
