<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getIbexa_Elasticsearch_Query_Common_AggregationResultExtractor_ContentTypeGroupTermService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'ibexa.elasticsearch.query.common.aggregation_result_extractor.content_type_group_term' shared autowired service.
     *
     * @return \Ibexa\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor\TermAggregationResultExtractor
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/elasticsearch/src/contracts/Query/AggregationResultExtractor.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/elasticsearch/src/lib/Query/ResultExtractor/AggregationResultExtractor/TermAggregationResultExtractor.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/elasticsearch/src/lib/Query/ResultExtractor/AggregationResultExtractor/TermAggregationKeyMapper.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/elasticsearch/src/lib/Query/ResultExtractor/AggregationResultExtractor/TermAggregationKeyMapper/ContentTypeGroupAggregationKeyMapper.php';

        $a = ($container->services['ibexa.api.service.content_type'] ?? $container->getIbexa_Api_Service_ContentTypeService());

        if (isset($container->privates['ibexa.elasticsearch.query.common.aggregation_result_extractor.content_type_group_term'])) {
            return $container->privates['ibexa.elasticsearch.query.common.aggregation_result_extractor.content_type_group_term'];
        }

        return $container->privates['ibexa.elasticsearch.query.common.aggregation_result_extractor.content_type_group_term'] = new \Ibexa\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor\TermAggregationResultExtractor('Ibexa\\Contracts\\Core\\Repository\\Values\\Content\\Query\\Aggregation\\ContentTypeGroupTermAggregation', new \Ibexa\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor\TermAggregationKeyMapper\ContentTypeGroupAggregationKeyMapper($a));
    }
}
