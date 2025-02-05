<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getTaxonomyEntryExtractorService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\Taxonomy\Extractor\TaxonomyEntryExtractor' shared autowired service.
     *
     * @return \Ibexa\Taxonomy\Extractor\TaxonomyEntryExtractor
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/taxonomy/src/lib/Extractor/TaxonomyEntryExtractorInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/taxonomy/src/lib/Extractor/TaxonomyEntryExtractor.php';

        return $container->privates['Ibexa\\Taxonomy\\Extractor\\TaxonomyEntryExtractor'] = new \Ibexa\Taxonomy\Extractor\TaxonomyEntryExtractor(($container->privates['Ibexa\\Taxonomy\\Service\\TaxonomyConfiguration'] ?? $container->getTaxonomyConfigurationService()));
    }
}
