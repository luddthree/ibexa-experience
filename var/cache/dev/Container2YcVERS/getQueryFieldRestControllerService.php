<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getQueryFieldRestControllerService extends App_KernelDevDebugContainer
{
    /**
     * Gets the public 'Ibexa\Bundle\FieldTypeQuery\Controller\QueryFieldRestController' shared autowired service.
     *
     * @return \Ibexa\Bundle\FieldTypeQuery\Controller\QueryFieldRestController
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/fieldtype-query/src/bundle/Controller/QueryFieldRestController.php';

        return $container->services['Ibexa\\Bundle\\FieldTypeQuery\\Controller\\QueryFieldRestController'] = new \Ibexa\Bundle\FieldTypeQuery\Controller\QueryFieldRestController(($container->services['Ibexa\\FieldTypeQuery\\QueryFieldService'] ?? $container->load('getQueryFieldServiceService')), ($container->services['ibexa.api.service.content'] ?? $container->getIbexa_Api_Service_ContentService()), ($container->services['ibexa.api.service.content_type'] ?? $container->getIbexa_Api_Service_ContentTypeService()), ($container->services['ibexa.api.service.location'] ?? $container->getIbexa_Api_Service_LocationService()), ($container->privates['Ibexa\\Bundle\\Rest\\RequestParser\\Router'] ?? $container->getRouter2Service()));
    }
}
