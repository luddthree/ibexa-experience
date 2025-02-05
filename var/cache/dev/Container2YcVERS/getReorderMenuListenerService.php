<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getReorderMenuListenerService extends App_KernelDevDebugContainer
{
    /**
     * Gets the public 'Ibexa\AdminUi\Menu\Admin\ReorderMenuListener' shared autowired service.
     *
     * @return \Ibexa\AdminUi\Menu\Admin\ReorderMenuListener
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/lib/Menu/Admin/ReorderMenuListener.php';

        return $container->services['Ibexa\\AdminUi\\Menu\\Admin\\ReorderMenuListener'] = new \Ibexa\AdminUi\Menu\Admin\ReorderMenuListener();
    }
}
