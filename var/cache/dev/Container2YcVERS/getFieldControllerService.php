<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getFieldControllerService extends App_KernelDevDebugContainer
{
    /**
     * Gets the public 'Ibexa\Bundle\FormBuilder\Controller\FieldController' shared autowired service.
     *
     * @return \Ibexa\Bundle\FormBuilder\Controller\FieldController
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/framework-bundle/Controller/AbstractController.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/contracts/Controller/Controller.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/form-builder/src/bundle/Controller/FieldController.php';

        $container->services['Ibexa\\Bundle\\FormBuilder\\Controller\\FieldController'] = $instance = new \Ibexa\Bundle\FormBuilder\Controller\FieldController(($container->privates['Ibexa\\FormBuilder\\Definition\\FieldDefinitionFactory'] ?? $container->getFieldDefinitionFactoryService()));

        $instance->setContainer($container);
        $instance->performAccessCheck();

        return $instance;
    }
}
