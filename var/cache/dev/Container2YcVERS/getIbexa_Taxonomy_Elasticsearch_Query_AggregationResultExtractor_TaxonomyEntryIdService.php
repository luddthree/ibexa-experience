<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getIbexa_Taxonomy_Elasticsearch_Query_AggregationResultExtractor_TaxonomyEntryIdService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'ibexa.taxonomy.elasticsearch.query.aggregation_result_extractor.taxonomy_entry_id' shared autowired service.
     *
     * @return \Ibexa\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor\TermAggregationResultExtractor
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/elasticsearch/src/contracts/Query/AggregationResultExtractor.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/elasticsearch/src/lib/Query/ResultExtractor/AggregationResultExtractor/TermAggregationResultExtractor.php';

        $a = ($container->privates['Ibexa\\Taxonomy\\Search\\Aggregation\\TaxonomyResultKeyMapper'] ?? $container->load('getTaxonomyResultKeyMapperService'));

        if (isset($container->privates['ibexa.taxonomy.elasticsearch.query.aggregation_result_extractor.taxonomy_entry_id'])) {
            return $container->privates['ibexa.taxonomy.elasticsearch.query.aggregation_result_extractor.taxonomy_entry_id'];
        }

        return $container->privates['ibexa.taxonomy.elasticsearch.query.aggregation_result_extractor.taxonomy_entry_id'] = new \Ibexa\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor\TermAggregationResultExtractor('Ibexa\\Contracts\\Taxonomy\\Search\\Query\\Aggregation\\TaxonomyEntryIdAggregation', $a);
    }
}
