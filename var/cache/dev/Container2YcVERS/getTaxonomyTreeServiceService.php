<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getTaxonomyTreeServiceService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\Taxonomy\Tree\TaxonomyTreeService' shared autowired service.
     *
     * @return \Ibexa\Taxonomy\Tree\TaxonomyTreeService
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/taxonomy/src/lib/Tree/TaxonomyTreeServiceInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/taxonomy/src/lib/Tree/TaxonomyTreeService.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/taxonomy/src/lib/Tree/TreeNodeMapper.php';

        $a = ($container->privates['Ibexa\\Core\\Repository\\Permission\\CachedPermissionService'] ?? $container->getCachedPermissionServiceService());

        if (isset($container->privates['Ibexa\\Taxonomy\\Tree\\TaxonomyTreeService'])) {
            return $container->privates['Ibexa\\Taxonomy\\Tree\\TaxonomyTreeService'];
        }

        return $container->privates['Ibexa\\Taxonomy\\Tree\\TaxonomyTreeService'] = new \Ibexa\Taxonomy\Tree\TaxonomyTreeService(($container->privates['Ibexa\\Taxonomy\\Persistence\\Repository\\TaxonomyEntryRepository'] ?? $container->getTaxonomyEntryRepositoryService()), new \Ibexa\Taxonomy\Tree\TreeNodeMapper(), $a, ($container->services['ibexa.api.service.language'] ?? $container->getIbexa_Api_Service_LanguageService()));
    }
}
