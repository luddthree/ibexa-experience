<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Customer;

use Ibexa\Personalization\Client\Consumer\AbstractPersonalizationConsumer;
use Ibexa\Personalization\Client\PersonalizationClientInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;

final class InformationDataFetcher extends AbstractPersonalizationConsumer implements InformationDataFetcherInterface
{
    private const ENDPOINT_URI = '/api/v4/base/get_mandator/%d';

    public const PARAM_BASE_INFORMATION = 'baseInformation';
    public const PARAM_CUSTOMER_DETAILS = 'customerInformation';
    public const PARAM_ADVANCED_OPTIONS = 'advancedOptions';
    public const PARAM_REGISTRATION_DATA = 'registrationData';
    public const PARAM_SECURITY_OPTIONS = 'securityOptions';
    public const PARAM_ITEM_TYPE_CONFIGURATION = 'itemTypeConfiguration';
    public const PARAM_PRODUCT_INFORMATION = 'productInformation';
    public const PARAM_ACCESS_CONTROL_LIST = 'accessControlList';
    public const ALLOWED_PARAMS = [
        self::PARAM_CUSTOMER_DETAILS,
        self::PARAM_ADVANCED_OPTIONS,
        self::PARAM_REGISTRATION_DATA,
        self::PARAM_SECURITY_OPTIONS,
        self::PARAM_ITEM_TYPE_CONFIGURATION,
        self::PARAM_PRODUCT_INFORMATION,
        self::PARAM_ACCESS_CONTROL_LIST,
    ];

    public function __construct(PersonalizationClientInterface $client, string $endPointUri)
    {
        parent::__construct($client, $endPointUri . self::ENDPOINT_URI);
    }

    public function fetchInformation(
        int $customerId,
        string $licenseKey,
        ?array $queryParams = null
    ): ResponseInterface {
        $uri = $this->buildEndPointUri(
            [
                $customerId,
            ]
        );

        $this->setAuthenticationParameters($customerId, $licenseKey);

        return $this->client->sendRequest(
            Request::METHOD_GET,
            $uri,
            $this->buildOptions($queryParams)
        );
    }

    private function buildOptions(?array $queryParams = null): array
    {
        if (empty($queryParams)) {
            return $this->getOptions();
        }

        return array_merge(
            [
                'query' => array_fill_keys(
                    array_intersect(self::ALLOWED_PARAMS, $queryParams),
                    true
                ),
            ],
            $this->getOptions()
        );
    }
}

class_alias(InformationDataFetcher::class, 'Ibexa\Platform\Personalization\Client\Consumer\Customer\InformationDataFetcher');
