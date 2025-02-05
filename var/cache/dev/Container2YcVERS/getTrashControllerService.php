<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getTrashControllerService extends App_KernelDevDebugContainer
{
    /**
     * Gets the public 'Ibexa\Bundle\AdminUi\Controller\TrashController' shared autowired service.
     *
     * @return \Ibexa\Bundle\AdminUi\Controller\TrashController
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/framework-bundle/Controller/AbstractController.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/contracts/Controller/Controller.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/bundle/Controller/TrashController.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/lib/Form/Factory/TrashFormFactory.php';

        $container->services['Ibexa\\Bundle\\AdminUi\\Controller\\TrashController'] = $instance = new \Ibexa\Bundle\AdminUi\Controller\TrashController(($container->privates['Ibexa\\AdminUi\\Notification\\TranslatableNotificationHandler'] ?? $container->getTranslatableNotificationHandlerService()), ($container->services['ibexa.api.service.trash'] ?? $container->getIbexa_Api_Service_TrashService()), ($container->services['ibexa.api.service.content_type'] ?? $container->getIbexa_Api_Service_ContentTypeService()), ($container->privates['Ibexa\\AdminUi\\UI\\Service\\PathService'] ?? $container->load('getPathServiceService')), new \Ibexa\AdminUi\Form\Factory\TrashFormFactory(($container->services['.container.private.form.factory'] ?? $container->get_Container_Private_Form_FactoryService())), ($container->privates['Ibexa\\AdminUi\\Form\\SubmitHandler'] ?? $container->load('getSubmitHandlerService')), ($container->privates['Ibexa\\Core\\MVC\\Symfony\\Locale\\UserLanguagePreferenceProvider'] ?? $container->getUserLanguagePreferenceProviderService()), ($container->services['Ibexa\\Bundle\\Core\\DependencyInjection\\Configuration\\ChainConfigResolver'] ?? $container->getChainConfigResolverService()), ($container->privates['Ibexa\\AdminUi\\QueryType\\TrashSearchQueryType'] ?? ($container->privates['Ibexa\\AdminUi\\QueryType\\TrashSearchQueryType'] = new \Ibexa\AdminUi\QueryType\TrashSearchQueryType())), ($container->services['ibexa.api.service.user'] ?? $container->getIbexa_Api_Service_UserService()));

        $instance->setContainer($container);
        $instance->performAccessCheck();

        return $instance;
    }
}
