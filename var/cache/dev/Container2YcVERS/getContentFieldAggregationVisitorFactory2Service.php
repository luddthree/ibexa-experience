<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getContentFieldAggregationVisitorFactory2Service extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'Ibexa\Solr\Query\Common\AggregationVisitor\Factory\ContentFieldAggregationVisitorFactory' shared autowired service.
     *
     * @return \Ibexa\Solr\Query\Common\AggregationVisitor\Factory\ContentFieldAggregationVisitorFactory
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/solr/src/lib/Query/Common/AggregationVisitor/Factory/ContentFieldAggregationVisitorFactory.php';

        return $container->privates['Ibexa\\Solr\\Query\\Common\\AggregationVisitor\\Factory\\ContentFieldAggregationVisitorFactory'] = new \Ibexa\Solr\Query\Common\AggregationVisitor\Factory\ContentFieldAggregationVisitorFactory(($container->privates['Ibexa\\Core\\Search\\Common\\FieldNameResolver'] ?? $container->getFieldNameResolverService()));
    }
}
