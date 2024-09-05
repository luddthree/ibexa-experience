<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Performance;

use Ibexa\Personalization\Client\Consumer\AbstractPersonalizationConsumer;
use Ibexa\Personalization\Client\PersonalizationClientInterface;
use Ibexa\Personalization\Service\Report\ReportService;
use Ibexa\Personalization\Value\DateTimeRange;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;

final class RevenueDetailsDataFetcher extends AbstractPersonalizationConsumer implements RevenueDetailsDataFetcherInterface
{
    public const ALLOWED_FORMATS = [
        ReportService::FORMAT_XLSX,
    ];

    private const ENDPOINT_URI = '/api/v4/%d/statistic/added_revenue%s';

    public function __construct(PersonalizationClientInterface $client, string $endPointUri)
    {
        parent::__construct($client, $endPointUri . self::ENDPOINT_URI);
    }

    public function fetchRevenueDetailsList(
        int $customerId,
        string $licenseKey,
        DateTimeRange $dateTimeRange
    ): ResponseInterface {
        $uri = $this->buildEndPointUri(
            [
                $customerId,
                null,
            ]
        );

        $this->setAuthenticationParameters($customerId, $licenseKey);

        return $this->client->sendRequest(
            Request::METHOD_GET,
            $uri,
            $this->buildOptions($dateTimeRange)
        );
    }

    public function fetchRevenueReport(
        int $customerId,
        string $licenseKey,
        DateTimeRange $dateTimeRange,
        string $format,
        ?string $email = null
    ): ResponseInterface {
        $uri = $this->buildEndPointUri(
            [
                $customerId,
                '.' . $format,
            ]
        );

        $this->setAuthenticationParameters($customerId, $licenseKey);

        return $this->client->sendRequest(
            Request::METHOD_GET,
            $uri,
            $this->buildOptions($dateTimeRange, $email)
        );
    }

    private function buildOptions(
        DateTimeRange $dateTimeRange,
        ?string $email = null
    ): array {
        $query = [
            'from_date_time' => $dateTimeRange->getFromDate()
                ->format(AbstractPersonalizationConsumer::DATE_TIME_SEPARATED_FORMAT),
            'to_date_time' => $dateTimeRange->getToDate()
                ->format(AbstractPersonalizationConsumer::DATE_TIME_SEPARATED_FORMAT),
        ];

        if (null !== $email) {
            $query['email'] = $email;
        }

        return array_merge(
            [
                'query' => $query,
            ],
            $this->getOptions()
        );
    }
}

class_alias(RevenueDetailsDataFetcher::class, 'Ibexa\Platform\Personalization\Client\Consumer\Performance\RevenueDetailsDataFetcher');
