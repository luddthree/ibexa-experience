<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getContentControllerService extends App_KernelDevDebugContainer
{
    /**
     * Gets the public 'Ibexa\Bundle\AdminUi\Controller\ContentController' shared autowired service.
     *
     * @return \Ibexa\Bundle\AdminUi\Controller\ContentController
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/framework-bundle/Controller/AbstractController.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/contracts/Controller/Controller.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/bundle/Controller/ContentController.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/contracts/Form/DataMapper/DataMapperInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/lib/Form/DataMapper/ContentMainLocationUpdateMapper.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/lib/Permission/LookupLimitationsTransformer.php';

        $container->services['Ibexa\\Bundle\\AdminUi\\Controller\\ContentController'] = $instance = new \Ibexa\Bundle\AdminUi\Controller\ContentController(($container->privates['Ibexa\\AdminUi\\Notification\\TranslatableNotificationHandler'] ?? $container->getTranslatableNotificationHandlerService()), ($container->services['ibexa.api.service.content'] ?? $container->getIbexa_Api_Service_ContentService()), ($container->privates['Ibexa\\AdminUi\\Form\\Factory\\FormFactory'] ?? $container->getFormFactory2Service()), ($container->privates['Ibexa\\AdminUi\\Form\\SubmitHandler'] ?? $container->load('getSubmitHandlerService')), new \Ibexa\AdminUi\Form\DataMapper\ContentMainLocationUpdateMapper(), ($container->privates['Ibexa\\CorporateAccount\\PageBuilder\\SiteAccess\\NonCorporateSiteAccessResolver'] ?? $container->load('getNonCorporateSiteAccessResolverService')), ($container->services['ibexa.api.service.location'] ?? $container->getIbexa_Api_Service_LocationService()), ($container->services['ibexa.api.service.user'] ?? $container->getIbexa_Api_Service_UserService()), ($container->privates['Ibexa\\Core\\Repository\\Permission\\CachedPermissionService'] ?? $container->getCachedPermissionServiceService()), ($container->privates['Ibexa\\AdminUi\\Permission\\LookupLimitationsTransformer'] ?? ($container->privates['Ibexa\\AdminUi\\Permission\\LookupLimitationsTransformer'] = new \Ibexa\AdminUi\Permission\LookupLimitationsTransformer())), ($container->privates['Ibexa\\Core\\Helper\\TranslationHelper'] ?? $container->getTranslationHelperService()), ($container->services['Ibexa\\Bundle\\Core\\DependencyInjection\\Configuration\\ChainConfigResolver'] ?? $container->getChainConfigResolverService()), ($container->privates['Ibexa\\SiteFactory\\SiteAccess\\SiteAccessNameGenerator'] ?? $container->getSiteAccessNameGeneratorService()), ($container->services['event_dispatcher'] ?? $container->getEventDispatcherService()), ($container->services['.container.private.form.factory'] ?? $container->get_Container_Private_Form_FactoryService()));

        $instance->setContainer($container);
        $instance->performAccessCheck();

        return $instance;
    }
}
