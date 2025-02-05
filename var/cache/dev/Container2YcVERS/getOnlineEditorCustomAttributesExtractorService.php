<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getOnlineEditorCustomAttributesExtractorService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\FieldTypeRichText\Translation\Extractor\OnlineEditorCustomAttributesExtractor' shared autowired service.
     *
     * @return \Ibexa\FieldTypeRichText\Translation\Extractor\OnlineEditorCustomAttributesExtractor
     *
     * @deprecated Since ibexa/fieldtype-richtext 4.6.7 the "Ibexa\FieldTypeRichText\Translation\Extractor\OnlineEditorCustomAttributesExtractor" service is deprecated, will be removed in 5.0.0
     */
    public static function do($container, $lazyLoad = true)
    {
        trigger_deprecation('', '', 'Since ibexa/fieldtype-richtext 4.6.7 the "Ibexa\\FieldTypeRichText\\Translation\\Extractor\\OnlineEditorCustomAttributesExtractor" service is deprecated, will be removed in 5.0.0');

        return new \Ibexa\FieldTypeRichText\Translation\Extractor\OnlineEditorCustomAttributesExtractor(($container->services['Ibexa\\Bundle\\Core\\DependencyInjection\\Configuration\\ChainConfigResolver'] ?? $container->getChainConfigResolverService()), $container->parameters['ibexa.site_access.list']);
    }
}
