<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getIbexa_Solr_Query_Common_AggregationVisitor_Field_AuthorTermService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'ibexa.solr.query.common.aggregation_visitor.field.author_term' shared autowired service.
     *
     * @return \Ibexa\Solr\Query\Common\AggregationVisitor\TermAggregationVisitor
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/solr/src/contracts/Query/AggregationVisitor.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/solr/src/lib/Query/Common/AggregationVisitor/AbstractTermAggregationVisitor.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/solr/src/lib/Query/Common/AggregationVisitor/TermAggregationVisitor.php';

        return $container->privates['ibexa.solr.query.common.aggregation_visitor.field.author_term'] = ($container->privates['Ibexa\\Solr\\Query\\Common\\AggregationVisitor\\Factory\\ContentFieldAggregationVisitorFactory'] ?? $container->load('getContentFieldAggregationVisitorFactory2Service'))->createTermAggregationVisitor('Ibexa\\Contracts\\Core\\Repository\\Values\\Content\\Query\\Aggregation\\Field\\AuthorTermAggregation', 'aggregation_value');
    }
}
