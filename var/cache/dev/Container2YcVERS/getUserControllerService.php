<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getUserControllerService extends App_KernelDevDebugContainer
{
    /**
     * Gets the public 'Ibexa\Bundle\ContentForms\Controller\UserController' shared service.
     *
     * @return \Ibexa\Bundle\ContentForms\Controller\UserController
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/framework-bundle/Controller/AbstractController.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/bundle/Core/Controller.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/content-forms/src/bundle/Controller/UserController.php';

        $container->services['Ibexa\\Bundle\\ContentForms\\Controller\\UserController'] = $instance = new \Ibexa\Bundle\ContentForms\Controller\UserController(($container->services['ibexa.api.service.content_type'] ?? $container->getIbexa_Api_Service_ContentTypeService()), ($container->services['ibexa.api.service.user'] ?? $container->getIbexa_Api_Service_UserService()), ($container->services['ibexa.api.service.location'] ?? $container->getIbexa_Api_Service_LocationService()), ($container->services['ibexa.api.service.language'] ?? $container->getIbexa_Api_Service_LanguageService()), ($container->privates['Ibexa\\ContentForms\\Form\\ActionDispatcher\\UserDispatcher'] ?? $container->load('getUserDispatcherService')), ($container->privates['Ibexa\\Core\\Repository\\Permission\\CachedPermissionService'] ?? $container->getCachedPermissionServiceService()), ($container->privates['Ibexa\\Core\\MVC\\Symfony\\Locale\\UserLanguagePreferenceProvider'] ?? $container->getUserLanguagePreferenceProviderService()), ($container->privates['Ibexa\\ContentForms\\Content\\Form\\Provider\\GroupedContentFormFieldsProvider'] ?? $container->load('getGroupedContentFormFieldsProviderService')), ($container->services['ibexa.api.service.content'] ?? $container->getIbexa_Api_Service_ContentService()));

        $instance->setContainer($container);

        return $instance;
    }
}
