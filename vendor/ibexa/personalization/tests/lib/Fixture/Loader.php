<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Fixture;

use RuntimeException;

final class Loader
{
    public const FIXTURES_DIR = '/fixtures';
    public const SCENARIO_FIXTURE = '/scenario/scenario-body.json';
    public const SCENARIO_LIST_FIXTURE = '/scenario/body.json';
    public const COMMERCE_SCENARIO_LIST_FIXTURE = '/scenario/commerce-scenario-list-body.json';
    public const PERFORMANCE_SUMMARY_FIXTURE = '/performance/summary-body.json';
    public const PERFORMANCE_SUMMARY_CLEAN_FIXTURE = '/performance/summary-clean-body.json';
    public const PERFORMANCE_EVENTS_SUMMARY_PUBLISHER_FIXTURE = '/performance/events-summary-publisher-body.json';
    public const PERFORMANCE_EVENTS_SUMMARY_COMMERCE_FIXTURE = '/performance/events-summary-commerce-body.json';
    public const PERFORMANCE_EVENTS_DETAILS_FIXTURE = '/performance/events-details-body.json';
    public const PERFORMANCE_REVENUE_DETAILS_LIST_NUMERIC_ID_FIXTURE = '/performance/revenue-details-list-with-numeric-ids-body.json';
    public const PERFORMANCE_REVENUE_DETAILS_LIST_ALPHANUMERIC_ID_FIXTURE = '/performance/revenue-details-list-with-alphanumeric-ids-body.json';
    public const PERFORMANCE_POPULARITY_LIST_FIXTURE = '/performance/popularity-body.json';
    public const CUSTOMER_EMPTY_FIXTURE = '/customer/empty-body.json';
    public const CUSTOMER_BASIC_FIXTURE = '/customer/body.json';
    public const CUSTOMER_EMPTY_FEATURES = '/customer/empty-features.json';
    public const CUSTOMER_BASIC_FEATURES = '/customer/features.json';
    public const CUSTOMER_EXTENDED_FIXTURE = '/customer/body-extended.json';
    public const CUSTOMER_ITEM_TYPE_CONFIGURATION_FIXTURE = '/customer/item-type-configuration-body.json';
    public const REPORT_RECOMMENDATION_DETAILED = '/report/recommendation_detailed.xlsx';
    public const REPORT_REVENUE = '/report/revenue.xlsx';
    public const MODEL_ATTRIBUTE_FIXTURE = '/model/attribute-values-body.json';
    public const SEARCH_ATTRIBUTES_FIXTURE = '/search/attribute-items-body.json';
    public const MODEL_SEGMENTS_FIXTURE = '/model/segments-values-body.json';
    public const RECOMMENDATIONS_FIXTURE = '/recommendations/response-body.json';
    public const MODEL_BUILD_STATUS_FIXTURE = '/modelBuild/model-build-status-body.json';
    public const TRIGGER_MODEL_BUILD_FIXTURE = '/modelBuild/trigger-model-build-body.json';
    public const CREATE_ACCOUNT_FIXTURE = '/account/create-account-response-body.json';

    public static function load(string $file): string
    {
        $file = dirname(__DIR__, 2) . self::FIXTURES_DIR . $file;
        $content = file_get_contents($file);

        if (false === $content) {
            throw new RuntimeException('Missing or not readable fixture file: ' . $file);
        }

        return $content;
    }
}

class_alias(Loader::class, 'Ibexa\Platform\Tests\Personalization\Fixture\Loader');
