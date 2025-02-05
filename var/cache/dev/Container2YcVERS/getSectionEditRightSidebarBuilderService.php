<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getSectionEditRightSidebarBuilderService extends App_KernelDevDebugContainer
{
    /**
     * Gets the public 'Ibexa\AdminUi\Menu\SectionEditRightSidebarBuilder' shared autowired service.
     *
     * @return \Ibexa\AdminUi\Menu\SectionEditRightSidebarBuilder
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/contracts/Menu/AbstractBuilder.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/lib/Menu/SectionEditRightSidebarBuilder.php';

        $a = ($container->services['Ibexa\\AdminUi\\Menu\\MenuItemFactory'] ?? $container->load('getMenuItemFactoryService'));

        if (isset($container->services['Ibexa\\AdminUi\\Menu\\SectionEditRightSidebarBuilder'])) {
            return $container->services['Ibexa\\AdminUi\\Menu\\SectionEditRightSidebarBuilder'];
        }
        $b = ($container->services['event_dispatcher'] ?? $container->getEventDispatcherService());

        if (isset($container->services['Ibexa\\AdminUi\\Menu\\SectionEditRightSidebarBuilder'])) {
            return $container->services['Ibexa\\AdminUi\\Menu\\SectionEditRightSidebarBuilder'];
        }

        return $container->services['Ibexa\\AdminUi\\Menu\\SectionEditRightSidebarBuilder'] = new \Ibexa\AdminUi\Menu\SectionEditRightSidebarBuilder($a, $b, ($container->services['Symfony\\Contracts\\Translation\\TranslatorInterface'] ?? $container->getTranslatorInterfaceService()));
    }
}
