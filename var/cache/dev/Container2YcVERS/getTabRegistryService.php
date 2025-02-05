<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getTabRegistryService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\AdminUi\Tab\TabRegistry' shared autowired service.
     *
     * @return \Ibexa\AdminUi\Tab\TabRegistry
     */
    public static function do($container, $lazyLoad = true)
    {
        if ($lazyLoad) {
            return $container->privates['Ibexa\\AdminUi\\Tab\\TabRegistry'] = $container->createProxy('TabRegistry_b3f938e', function () use ($container) {
                return \TabRegistry_b3f938e::staticProxyConstructor(function (&$wrappedInstance, \ProxyManager\Proxy\LazyLoadingInterface $proxy) use ($container) {
                    $wrappedInstance = self::do($container, false);

                    $proxy->setProxyInitializer(null);

                    return true;
                });
            });
        }

        include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/lib/Tab/TabRegistry.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/lib/Tab/TabGroup.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/contracts/Tab/TabInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/contracts/Tab/AbstractTab.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/contracts/Tab/AbstractEventDispatchingTab.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/contracts/Tab/OrderedTabInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/contracts/Tab/ConditionalTabInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/lib/Tab/LocationView/PoliciesTab.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/lib/Tab/LocationView/RolesTab.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/seo/src/lib/UI/Tab/SeoContentTab.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/admin-ui/src/lib/Tab/LocationView/UrlsTab.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/product-catalog/src/lib/Tab/Product/UrlsTab.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/taxonomy/src/lib/UI/Tab/AssignedContentTab.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/form-builder/src/lib/Tab/LocationView/SubmissionsTab.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/segmentation/src/lib/Tab/UserViewTab.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/seo/src/lib/Content/SeoFieldResolverInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/seo/src/lib/Content/SeoFieldResolver.php';

        $instance = new \Ibexa\AdminUi\Tab\TabRegistry();

        $a = ($container->services['.container.private.twig'] ?? $container->get_Container_Private_TwigService());
        $b = ($container->services['Symfony\\Contracts\\Translation\\TranslatorInterface'] ?? $container->getTranslatorInterfaceService());
        $c = ($container->privates['Ibexa\\AdminUi\\UI\\Dataset\\DatasetFactory'] ?? $container->load('getDatasetFactoryService'));
        $d = ($container->privates['Ibexa\\Core\\Repository\\Permission\\CachedPermissionService'] ?? $container->getCachedPermissionServiceService());
        $e = ($container->services['event_dispatcher'] ?? $container->getEventDispatcherService());
        $f = ($container->services['Ibexa\\Bundle\\Core\\DependencyInjection\\Configuration\\ChainConfigResolver'] ?? $container->getChainConfigResolverService());
        $g = ($container->services['request_stack'] ?? ($container->services['request_stack'] = new \Symfony\Component\HttpFoundation\RequestStack()));
        $h = ($container->services['ibexa.api.service.language'] ?? $container->getIbexa_Api_Service_LanguageService());

        $instance->addTabGroup(new \Ibexa\AdminUi\Tab\TabGroup('systeminfo'));
        $instance->addTab(($container->services['Ibexa\\AdminUi\\Tab\\LocationView\\AuthorsTab'] ?? $container->load('getAuthorsTabService')), 'location-view');
        $instance->addTab(($container->services['Ibexa\\AdminUi\\Tab\\LocationView\\ContentTab'] ?? $container->load('getContentTabService')), 'location-view');
        $instance->addTab(($container->services['Ibexa\\AdminUi\\Tab\\LocationView\\DetailsTab'] ?? $container->load('getDetailsTabService')), 'location-view');
        $instance->addTab(($container->services['Ibexa\\AdminUi\\Tab\\LocationView\\LocationsTab'] ?? $container->load('getLocationsTabService')), 'location-view');
        $instance->addTab(new \Ibexa\AdminUi\Tab\LocationView\PoliciesTab($a, $b, $c, $d, $e, $f), 'location-view');
        $instance->addTab(($container->services['Ibexa\\AdminUi\\Tab\\LocationView\\RelationsTab'] ?? $container->load('getRelationsTabService')), 'location-view');
        $instance->addTab(new \Ibexa\AdminUi\Tab\LocationView\RolesTab($a, $b, $c, $d, $e, $f), 'location-view');
        $instance->addTab(($container->services['Ibexa\\AdminUi\\Tab\\LocationView\\SubItemsTab'] ?? $container->load('getSubItemsTabService')), 'location-view');
        $instance->addTab(($container->services['Ibexa\\AdminUi\\Tab\\LocationView\\TranslationsTab'] ?? $container->load('getTranslationsTab2Service')), 'location-view');
        $instance->addTab(($container->privates['Ibexa\\AdminUi\\Tab\\LocationView\\UrlsTab'] ?? $container->load('getUrlsTabService')), 'location-view');
        $instance->addTab(($container->services['Ibexa\\AdminUi\\Tab\\LocationView\\VersionsTab'] ?? $container->load('getVersionsTabService')), 'location-view');
        $instance->addTab(($container->services['Ibexa\\AdminUi\\Tab\\ContentType\\TranslationsTab'] ?? $container->load('getTranslationsTabService')), 'content-type');
        $instance->addTab(($container->services['Ibexa\\AdminUi\\Tab\\ContentType\\ViewTab'] ?? $container->load('getViewTabService')), 'content-type');
        $instance->addTab(($container->privates['Ibexa\\AdminUi\\Tab\\URLManagement\\URLWildcardsTab'] ?? $container->load('getURLWildcardsTabService')), 'link-manager');
        $instance->addTab(($container->privates['Ibexa\\AdminUi\\Tab\\URLManagement\\LinkManagerTab'] ?? $container->load('getLinkManagerTabService')), 'link-manager');
        $instance->addTab(($container->privates['Ibexa\\AdminUi\\Tab\\Dashboard\\MyDraftsTab'] ?? $container->load('getMyDraftsTabService')), 'dashboard-my');
        $instance->addTab(($container->privates['Ibexa\\AdminUi\\Tab\\Dashboard\\MyContentTab'] ?? $container->load('getMyContentTabService')), 'dashboard-my');
        $instance->addTab(($container->privates['Ibexa\\AdminUi\\Tab\\Dashboard\\EveryoneContentTab'] ?? $container->load('getEveryoneContentTabService')), 'dashboard-everyone');
        $instance->addTab(($container->privates['Ibexa\\AdminUi\\Tab\\Dashboard\\MyMediaTab'] ?? $container->load('getMyMediaTabService')), 'dashboard-my');
        $instance->addTab(($container->privates['Ibexa\\AdminUi\\Tab\\Dashboard\\EveryoneMediaTab'] ?? $container->load('getEveryoneMediaTabService')), 'dashboard-everyone');
        $instance->addTab(($container->privates['Ibexa\\Scheduler\\Dashboard\\MyScheduledTab'] ?? $container->load('getMyScheduledTabService')), 'dashboard-my');
        $instance->addTab(($container->privates['Ibexa\\Scheduler\\Dashboard\\AllScheduledTab'] ?? $container->load('getAllScheduledTabService')), 'dashboard-everyone');
        $instance->addTab(($container->privates['Ibexa\\Workflow\\Tab\\MyDraftsUnderReviewTab'] ?? $container->load('getMyDraftsUnderReviewTabService')), 'dashboard-my');
        $instance->addTab(new \Ibexa\Seo\UI\Tab\SeoContentTab($a, $b, $e, ($container->privates['Ibexa\\Seo\\Content\\SeoFieldResolver'] ?? ($container->privates['Ibexa\\Seo\\Content\\SeoFieldResolver'] = new \Ibexa\Seo\Content\SeoFieldResolver()))), 'location-view');
        $instance->addTab(($container->privates['Ibexa\\ProductCatalog\\Tab\\AttributeDefinition\\TranslationsTab'] ?? $container->load('getTranslationsTab3Service')), 'attribute-definition');
        $instance->addTab(($container->privates['Ibexa\\ProductCatalog\\Tab\\AttributeDefinition\\DetailsTab'] ?? $container->load('getDetailsTab2Service')), 'attribute-definition');
        $instance->addTab(($container->privates['Ibexa\\ProductCatalog\\Tab\\AttributeGroup\\TranslationsTab'] ?? $container->load('getTranslationsTab4Service')), 'attribute-group');
        $instance->addTab(($container->privates['Ibexa\\ProductCatalog\\Tab\\AttributeGroup\\DetailsTab'] ?? $container->load('getDetailsTab3Service')), 'attribute-group');
        $instance->addTab(($container->privates['Ibexa\\ProductCatalog\\Tab\\AttributeGroup\\AttributesTab'] ?? $container->load('getAttributesTabService')), 'attribute-group');
        $instance->addTab(($container->privates['Ibexa\\ProductCatalog\\Tab\\Catalog\\DetailsTab'] ?? $container->load('getDetailsTab4Service')), 'catalog');
        $instance->addTab(($container->privates['Ibexa\\ProductCatalog\\Tab\\Catalog\\TranslationsTab'] ?? $container->load('getTranslationsTab5Service')), 'catalog');
        $instance->addTab(($container->privates['Ibexa\\ProductCatalog\\Tab\\Catalog\\ProductsTab'] ?? $container->load('getProductsTabService')), 'catalog');
        $instance->addTab(($container->privates['Ibexa\\ProductCatalog\\Tab\\CustomerGroup\\TranslationsTab'] ?? $container->load('getTranslationsTab6Service')), 'customer-group');
        $instance->addTab(($container->privates['Ibexa\\ProductCatalog\\Tab\\CustomerGroup\\DetailsTab'] ?? $container->load('getDetailsTab5Service')), 'customer-group');
        $instance->addTab(($container->privates['Ibexa\\ProductCatalog\\Tab\\Product\\DetailsTab'] ?? $container->load('getDetailsTab6Service')), 'product');
        $instance->addTab(($container->privates['Ibexa\\ProductCatalog\\Tab\\Product\\AttributesTab'] ?? $container->load('getAttributesTab2Service')), 'product');
        $instance->addTab(($container->privates['Ibexa\\ProductCatalog\\Tab\\Product\\PricesTab'] ?? $container->load('getPricesTabService')), 'product');
        $instance->addTab(($container->privates['Ibexa\\ProductCatalog\\Tab\\Product\\AvailabilityTab'] ?? $container->load('getAvailabilityTabService')), 'product');
        $instance->addTab(($container->privates['Ibexa\\ProductCatalog\\Tab\\Product\\TranslationsTab'] ?? $container->load('getTranslationsTab7Service')), 'product');
        $instance->addTab(($container->privates['Ibexa\\ProductCatalog\\Tab\\Product\\AssetsTab'] ?? $container->load('getAssetsTabService')), 'product');
        $instance->addTab(($container->privates['Ibexa\\ProductCatalog\\Tab\\Product\\CompletenessTab'] ?? $container->load('getCompletenessTabService')), 'product');
        $instance->addTab(($container->privates['Ibexa\\ProductCatalog\\Tab\\Product\\VariantsTab'] ?? $container->load('getVariantsTabService')), 'product');
        $instance->addTab(new \Ibexa\ProductCatalog\Tab\Product\UrlsTab($a, $b, ($container->services['ibexa.api.service.url_alias'] ?? $container->getIbexa_Api_Service_UrlAliasService()), ($container->privates['Ibexa\\AdminUi\\Form\\Factory\\FormFactory'] ?? $container->getFormFactory2Service()), $c, ($container->services['ibexa.api.service.location'] ?? $container->getIbexa_Api_Service_LocationService()), $d, $e, ($container->privates['Ibexa\\Core\\Helper\\TranslationHelper'] ?? $container->getTranslationHelperService()), $f, $g), 'product');
        $instance->addTab(($container->privates['Ibexa\\ProductCatalog\\Tab\\Category\\ProductsTab'] ?? $container->load('getProductsTab2Service')), 'location-view');
        $instance->addTab(($container->privates['Ibexa\\ProductCatalog\\Tab\\ProductType\\DetailsTab'] ?? $container->load('getDetailsTab7Service')), 'product-type');
        $instance->addTab(($container->privates['Ibexa\\ProductCatalog\\Tab\\ProductType\\TranslationTab'] ?? $container->load('getTranslationTabService')), 'product-type');
        $instance->addTab(new \Ibexa\Taxonomy\UI\Tab\AssignedContentTab($a, $b, $e, ($container->privates['Ibexa\\Taxonomy\\Service\\TaxonomyConfiguration'] ?? $container->getTaxonomyConfigurationService()), ($container->privates['Ibexa\\Taxonomy\\Service\\Event\\TaxonomyService'] ?? $container->getTaxonomyServiceService()), ($container->services['ibexa.api.service.search'] ?? $container->getIbexa_Api_Service_SearchService()), ($container->privates['Ibexa\\AdminUi\\UI\\Value\\Content\\Location\\Mapper'] ?? $container->load('getMapperService')), $g, $h, ($container->services['.container.private.form.factory'] ?? $container->get_Container_Private_Form_FactoryService()), $f, $d), 'location-view');
        $instance->addTab(($container->services['Ibexa\\Bundle\\SiteContext\\UI\\Tabs\\PreviewTab'] ?? $container->load('getPreviewTabService')), 'location-view');
        $instance->addTab(new \Ibexa\FormBuilder\Tab\LocationView\SubmissionsTab($a, $b, ($container->services['Ibexa\\FormBuilder\\FormSubmission\\FormSubmissionService'] ?? $container->load('getFormSubmissionServiceService')), ($container->services['Ibexa\\FormBuilder\\FieldType\\FormFactory'] ?? $container->getFormFactoryService()), ($container->services['ibexa.api.service.content_type'] ?? $container->getIbexa_Api_Service_ContentTypeService()), $h, ($container->services['Ibexa\\FormBuilder\\FieldType\\Type'] ?? $container->getType6Service()), $f), 'location-view');
        $instance->addTab(new \Ibexa\Segmentation\Tab\UserViewTab($a, $b, $e, $d, $f, ($container->services['ibexa.api.service.user'] ?? $container->getIbexa_Api_Service_UserService()), ($container->privates['Ibexa\\Segmentation\\Service\\Event\\SegmentationServiceEventDecorator'] ?? $container->getSegmentationServiceEventDecoratorService()), $g), 'location-view');

        return $instance;
    }
}
