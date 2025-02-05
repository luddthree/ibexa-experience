<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getViewMatcherRegistryService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\Bundle\Core\Matcher\ViewMatcherRegistry' shared service.
     *
     * @return \Ibexa\Bundle\Core\Matcher\ViewMatcherRegistry
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/contracts/MVC/View/ViewMatcherRegistryInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/core/src/bundle/Core/Matcher/ViewMatcherRegistry.php';

        return $container->privates['Ibexa\\Bundle\\Core\\Matcher\\ViewMatcherRegistry'] = new \Ibexa\Bundle\Core\Matcher\ViewMatcherRegistry(new RewindableGenerator(function () use ($container) {
            yield 'Ibexa\\FieldTypeQuery\\ContentView\\FieldDefinitionIdentifierMatcher' => ($container->privates['Ibexa\\FieldTypeQuery\\ContentView\\FieldDefinitionIdentifierMatcher'] ?? $container->load('getFieldDefinitionIdentifierMatcherService'));
            yield 'Ibexa\\Contracts\\ProductCatalog\\ViewMatcher\\ProductBased\\AttributeValue' => ($container->privates['Ibexa\\Contracts\\ProductCatalog\\ViewMatcher\\ProductBased\\AttributeValue'] ?? $container->load('getAttributeValueService'));
            yield 'Ibexa\\Contracts\\ProductCatalog\\ViewMatcher\\ProductBased\\IsAvailable' => ($container->privates['Ibexa\\Contracts\\ProductCatalog\\ViewMatcher\\ProductBased\\IsAvailable'] ?? $container->load('getIsAvailableService'));
            yield 'Ibexa\\Contracts\\ProductCatalog\\ViewMatcher\\ProductBased\\IsBaseProduct' => ($container->privates['Ibexa\\Contracts\\ProductCatalog\\ViewMatcher\\ProductBased\\IsBaseProduct'] ?? $container->load('getIsBaseProductService'));
            yield 'Ibexa\\Contracts\\ProductCatalog\\ViewMatcher\\ProductBased\\IsProduct' => ($container->privates['Ibexa\\Contracts\\ProductCatalog\\ViewMatcher\\ProductBased\\IsProduct'] ?? $container->load('getIsProductService'));
            yield 'Ibexa\\Contracts\\ProductCatalog\\ViewMatcher\\ProductBased\\ProductCode' => ($container->privates['Ibexa\\Contracts\\ProductCatalog\\ViewMatcher\\ProductBased\\ProductCode'] ?? $container->load('getProductCodeService'));
            yield 'Ibexa\\Contracts\\ProductCatalog\\ViewMatcher\\ProductBased\\ProductType' => ($container->privates['Ibexa\\Contracts\\ProductCatalog\\ViewMatcher\\ProductBased\\ProductType'] ?? $container->load('getProductTypeService'));
            yield 'Ibexa\\Contracts\\ProductCatalog\\ViewMatcher\\ProductTypeBased\\IsProduct' => ($container->privates['Ibexa\\Contracts\\ProductCatalog\\ViewMatcher\\ProductTypeBased\\IsProduct'] ?? ($container->privates['Ibexa\\Contracts\\ProductCatalog\\ViewMatcher\\ProductTypeBased\\IsProduct'] = new \Ibexa\Bundle\ProductCatalog\View\Matcher\ProductTypeBased\IsProduct()));
            yield 'Ibexa\\Contracts\\ProductCatalog\\ViewMatcher\\LocationBased\\RootLocation' => ($container->privates['Ibexa\\Contracts\\ProductCatalog\\ViewMatcher\\LocationBased\\RootLocation'] ?? $container->load('getRootLocationService'));
            yield 'Ibexa\\Taxonomy\\View\\Matcher\\IsTaxonomy' => ($container->privates['Ibexa\\Taxonomy\\View\\Matcher\\IsTaxonomy'] ?? $container->load('getIsTaxonomyService'));
            yield 'Ibexa\\Taxonomy\\View\\Matcher\\IsTaxonomyParentFolder' => ($container->privates['Ibexa\\Taxonomy\\View\\Matcher\\IsTaxonomyParentFolder'] ?? $container->load('getIsTaxonomyParentFolderService'));
            yield 'Ibexa\\Taxonomy\\View\\Matcher\\TaxonomyEntryBased\\Id' => ($container->privates['Ibexa\\Taxonomy\\View\\Matcher\\TaxonomyEntryBased\\Id'] ?? $container->load('getIdService'));
            yield 'Ibexa\\Taxonomy\\View\\Matcher\\TaxonomyEntryBased\\Identifier' => ($container->privates['Ibexa\\Taxonomy\\View\\Matcher\\TaxonomyEntryBased\\Identifier'] ?? $container->load('getIdentifierService'));
            yield 'Ibexa\\Taxonomy\\View\\Matcher\\TaxonomyEntryBased\\Level' => ($container->privates['Ibexa\\Taxonomy\\View\\Matcher\\TaxonomyEntryBased\\Level'] ?? $container->load('getLevelService'));
            yield 'Ibexa\\Taxonomy\\View\\Matcher\\TaxonomyEntryBased\\Taxonomy' => ($container->privates['Ibexa\\Taxonomy\\View\\Matcher\\TaxonomyEntryBased\\Taxonomy'] ?? $container->load('getTaxonomyService'));
            yield 'Ibexa\\SiteContext\\Matcher\\IsFullscreen' => ($container->privates['Ibexa\\SiteContext\\Matcher\\IsFullscreen'] ?? $container->load('getIsFullscreenService'));
            yield 'Ibexa\\CorporateAccount\\View\\Matcher\\IsSiteAccessRoot' => ($container->privates['Ibexa\\CorporateAccount\\View\\Matcher\\IsSiteAccessRoot'] ?? $container->load('getIsSiteAccessRootService'));
            yield 'Ibexa\\IsDashboard' => ($container->services['Ibexa\\Bundle\\Dashboard\\ViewMatcher\\IsDashboardMatcher'] ?? $container->load('getIsDashboardMatcherService'));
        }, 18));
    }
}
