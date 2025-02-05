<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getSuggestReviewerControllerService extends App_KernelDevDebugContainer
{
    /**
     * Gets the public 'Ibexa\Bundle\Workflow\Controller\SuggestReviewerController' shared autowired service.
     *
     * @return \Ibexa\Bundle\Workflow\Controller\SuggestReviewerController
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/framework-bundle/Controller/AbstractController.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/contracts/Controller/Controller.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/workflow/src/bundle/Controller/SuggestReviewerController.php';

        $container->services['Ibexa\\Bundle\\Workflow\\Controller\\SuggestReviewerController'] = $instance = new \Ibexa\Bundle\Workflow\Controller\SuggestReviewerController(($container->services['ibexa.api.service.search'] ?? $container->getIbexa_Api_Service_SearchService()), ($container->privates['Ibexa\\Core\\Repository\\Permission\\CachedPermissionService'] ?? $container->getCachedPermissionServiceService()), ($container->services['Ibexa\\Bundle\\Core\\DependencyInjection\\Configuration\\ChainConfigResolver'] ?? $container->getChainConfigResolverService()), ($container->services['ibexa.api.service.content'] ?? $container->getIbexa_Api_Service_ContentService()), ($container->services['ibexa.api.service.location'] ?? $container->getIbexa_Api_Service_LocationService()), ($container->privates['Ibexa\\Workflow\\QueryType\\UsersQueryType'] ?? ($container->privates['Ibexa\\Workflow\\QueryType\\UsersQueryType'] = new \Ibexa\Workflow\QueryType\UsersQueryType())), ($container->privates['Ibexa\\Workflow\\Registry\\WorkflowDefinitionMetadataRegistry'] ?? $container->getWorkflowDefinitionMetadataRegistryService()));

        $instance->setContainer(($container->privates['.service_locator.mx0UMmY'] ?? $container->load('get_ServiceLocator_Mx0UMmYService'))->withContext('Ibexa\\Bundle\\Workflow\\Controller\\SuggestReviewerController', $container));

        return $instance;
    }
}
