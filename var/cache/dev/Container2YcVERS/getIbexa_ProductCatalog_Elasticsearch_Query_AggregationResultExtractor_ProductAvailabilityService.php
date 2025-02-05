<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getIbexa_ProductCatalog_Elasticsearch_Query_AggregationResultExtractor_ProductAvailabilityService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'ibexa.product_catalog.elasticsearch.query.aggregation_result_extractor.product_availability' shared autowired service.
     *
     * @return \Ibexa\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor\TermAggregationResultExtractor
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/elasticsearch/src/contracts/Query/AggregationResultExtractor.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/elasticsearch/src/lib/Query/ResultExtractor/AggregationResultExtractor/TermAggregationResultExtractor.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/elasticsearch/src/lib/Query/ResultExtractor/AggregationResultExtractor/TermAggregationKeyMapper.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/elasticsearch/src/lib/Query/ResultExtractor/AggregationResultExtractor/TermAggregationKeyMapper/BooleanAggregationKeyMapper.php';

        return $container->privates['ibexa.product_catalog.elasticsearch.query.aggregation_result_extractor.product_availability'] = new \Ibexa\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor\TermAggregationResultExtractor('Ibexa\\Contracts\\ProductCatalog\\Values\\Product\\Query\\Aggregation\\ProductAvailabilityTermAggregation', ($container->privates['Ibexa\\Elasticsearch\\Query\\ResultExtractor\\AggregationResultExtractor\\TermAggregationKeyMapper\\BooleanAggregationKeyMapper'] ?? ($container->privates['Ibexa\\Elasticsearch\\Query\\ResultExtractor\\AggregationResultExtractor\\TermAggregationKeyMapper\\BooleanAggregationKeyMapper'] = new \Ibexa\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor\TermAggregationKeyMapper\BooleanAggregationKeyMapper())));
    }
}
