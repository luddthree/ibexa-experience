<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getProductTypeChoiceTypeService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\Bundle\ProductCatalog\Form\Type\ProductTypeChoiceType' shared autowired service.
     *
     * @return \Ibexa\Bundle\ProductCatalog\Form\Type\ProductTypeChoiceType
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/form/FormTypeInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/symfony/form/AbstractType.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/product-catalog/src/bundle/Form/Type/ProductTypeChoiceType.php';

        $a = ($container->privates['Ibexa\\ProductCatalog\\Dispatcher\\ProductTypeServiceDispatcher'] ?? $container->getProductTypeServiceDispatcherService());

        if (isset($container->privates['Ibexa\\Bundle\\ProductCatalog\\Form\\Type\\ProductTypeChoiceType'])) {
            return $container->privates['Ibexa\\Bundle\\ProductCatalog\\Form\\Type\\ProductTypeChoiceType'];
        }

        return $container->privates['Ibexa\\Bundle\\ProductCatalog\\Form\\Type\\ProductTypeChoiceType'] = new \Ibexa\Bundle\ProductCatalog\Form\Type\ProductTypeChoiceType($a);
    }
}
