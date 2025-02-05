<?php

namespace Container2YcVERS;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getIbexa_Solr_Query_Common_AggregationResultExtractor_Field_FloatRangeService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'ibexa.solr.query.common.aggregation_result_extractor.field.float_range' shared autowired service.
     *
     * @return \Ibexa\Solr\ResultExtractor\AggregationResultExtractor\RangeAggregationResultExtractor
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/solr/src/contracts/ResultExtractor/AggregationResultExtractor.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/solr/src/lib/ResultExtractor/AggregationResultExtractor/RangeAggregationResultExtractor.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/solr/src/contracts/ResultExtractor/AggregationResultExtractor/RangeAggregationKeyMapper.php';
        include_once \dirname(__DIR__, 4).'/vendor/ibexa/solr/src/lib/ResultExtractor/AggregationResultExtractor/RangeAggregationKeyMapper/FloatRangeAggregationKeyMapper.php';

        return $container->privates['ibexa.solr.query.common.aggregation_result_extractor.field.float_range'] = new \Ibexa\Solr\ResultExtractor\AggregationResultExtractor\RangeAggregationResultExtractor('Ibexa\\Contracts\\Core\\Repository\\Values\\Content\\Query\\Aggregation\\Field\\FloatRangeAggregation', ($container->privates['Ibexa\\Solr\\ResultExtractor\\AggregationResultExtractor\\RangeAggregationKeyMapper\\FloatRangeAggregationKeyMapper'] ?? ($container->privates['Ibexa\\Solr\\ResultExtractor\\AggregationResultExtractor\\RangeAggregationKeyMapper\\FloatRangeAggregationKeyMapper'] = new \Ibexa\Solr\ResultExtractor\AggregationResultExtractor\RangeAggregationKeyMapper\FloatRangeAggregationKeyMapper())));
    }
}
