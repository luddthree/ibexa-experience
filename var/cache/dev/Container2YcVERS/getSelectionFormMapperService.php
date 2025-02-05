<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getSelectionFormMapperService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\Bundle\ProductCatalog\Form\CatalogFilter\Attributes\SelectionFormMapper' shared autowired service.
     *
     * @return \Ibexa\Bundle\ProductCatalog\Form\CatalogFilter\Attributes\SelectionFormMapper
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/product-catalog/src/contracts/CatalogFilters/FilterFormMapperInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/product-catalog/src/bundle/Form/CatalogFilter/Attributes/SelectionFormMapper.php';

        return $container->privates['Ibexa\\Bundle\\ProductCatalog\\Form\\CatalogFilter\\Attributes\\SelectionFormMapper'] = new \Ibexa\Bundle\ProductCatalog\Form\CatalogFilter\Attributes\SelectionFormMapper(($container->privates['Ibexa\\Bundle\\Core\\SiteAccess\\LanguageResolver'] ?? $container->getLanguageResolverService()));
    }
}
