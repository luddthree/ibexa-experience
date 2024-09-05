<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Model;

use Ibexa\Personalization\Client\Consumer\AbstractPersonalizationConsumer;
use Ibexa\Personalization\Client\PersonalizationClientInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;

final class AttributeListDataFetcher extends AbstractPersonalizationConsumer implements AttributeListDataFetcherInterface
{
    private const ENDPOINT_URI = '/api/v3/%d/structure/get_attribute_pks';
    public const PARAM_ATTRIBUTE_LIST = 'attributePkList';

    public function __construct(
        PersonalizationClientInterface $client,
        string $endPointUri
    ) {
        parent::__construct($client, $endPointUri . self::ENDPOINT_URI);
    }

    public function fetchAttributeList(
        int $customerId,
        string $licenseKey
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
            $this->getOptions()
        );
    }
}

class_alias(AttributeListDataFetcher::class, 'Ibexa\Platform\Personalization\Client\Consumer\Model\AttributeListDataFetcher');
