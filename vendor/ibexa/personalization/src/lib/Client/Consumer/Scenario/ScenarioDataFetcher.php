<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Scenario;

use Ibexa\Personalization\Client\Consumer\AbstractPersonalizationConsumer;
use Ibexa\Personalization\Client\PersonalizationClientInterface;
use Ibexa\Personalization\Value\GranularityDateTimeRange;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
final class ScenarioDataFetcher extends AbstractPersonalizationConsumer implements ScenarioDataFetcherInterface
{
    public const SCENARIO_FILTER_STANDARD = 'standard';
    public const SCENARIO_FILTER_PROFILE = 'profile';
    public const KEY_SCENARIO_INFO_LIST = 'scenarioInfoList';

    private const ENDPOINT_URI = '/api/v3/%d/structure/%s';
    private const SCENARIO_LIST_ACTION = 'get_scenario_list';
    private const SCENARIO_DETAIL_ACTION = 'get_scenario/%s';
    private const SCENARIO_FILTER_SET = 'get_filter_set/%s/%s';

    public function __construct(PersonalizationClientInterface $client, string $endPointUri)
    {
        parent::__construct($client, $endPointUri . self::ENDPOINT_URI);
    }

    public function fetchScenarioList(
        int $customerId,
        string $licenseKey,
        ?GranularityDateTimeRange $granularityDateTimeRange = null
    ): ResponseInterface {
        $uri = $this->buildEndPointUri(
            [
                $customerId,
                self::SCENARIO_LIST_ACTION,
            ]
        );

        $this->setAuthenticationParameters($customerId, $licenseKey);

        $options =
            null !== $granularityDateTimeRange
                ? $this->buildOptions($granularityDateTimeRange)
                : $this->getOptions();

        return $this->client->sendRequest(
            Request::METHOD_GET,
            $uri,
            $options
        );
    }

    public function fetchScenarioListByScenarioType(
        int $customerId,
        string $licenseKey,
        string $scenarioType
    ): ResponseInterface {
        $uri = $this->buildEndPointUri(
            [
                $customerId,
                self::SCENARIO_LIST_ACTION,
            ]
        );

        $this->setAuthenticationParameters($customerId, $licenseKey);

        return $this->client->sendRequest(
            Request::METHOD_GET,
            $uri,
            array_merge(
                $this->getOptions(),
                [
                    'query' => [
                        'scenario_type' => $scenarioType,
                    ],
                ]
            )
        );
    }

    public function fetchScenario(
        int $customerId,
        string $licenseKey,
        string $scenarioName
    ): ResponseInterface {
        $uri = $this->buildEndPointUri(
            [
                $customerId,
                sprintf(
                    self::SCENARIO_DETAIL_ACTION,
                    $scenarioName
                ),
            ]
        );

        $this->setAuthenticationParameters($customerId, $licenseKey);

        return $this->client->sendRequest(
            Request::METHOD_GET,
            $uri,
            $this->getOptions()
        );
    }

    public function fetchScenarioFilterSet(
        int $customerId,
        string $licenseKey,
        string $filterType,
        string $scenarioName
    ): ResponseInterface {
        $uri = $this->buildEndPointUri(
            [
                $customerId,
                sprintf(
                    self::SCENARIO_FILTER_SET,
                    $filterType,
                    $scenarioName
                ),
            ]
        );

        $this->setAuthenticationParameters($customerId, $licenseKey);

        return $this->client->sendRequest(
            Request::METHOD_GET,
            $uri,
            $this->getOptions()
        );
    }

    private function buildOptions(GranularityDateTimeRange $granularityDateTimeRange): array
    {
        $startDate = $granularityDateTimeRange->getFromDate()
            ->format(AbstractPersonalizationConsumer::DATE_TIME_SEPARATED_FORMAT);
        $endDate = $granularityDateTimeRange->getToDate()
            ->format(AbstractPersonalizationConsumer::DATE_TIME_SEPARATED_FORMAT);

        return array_merge(
            [
                'query' => [
                    'granularity' => $granularityDateTimeRange->getGranularity(),
                    'from_date_time' => $startDate,
                    'to_date_time' => $endDate,
                ],
            ],
            $this->getOptions(),
        );
    }
}

class_alias(ScenarioDataFetcher::class, 'Ibexa\Platform\Personalization\Client\Consumer\Scenario\ScenarioDataFetcher');
