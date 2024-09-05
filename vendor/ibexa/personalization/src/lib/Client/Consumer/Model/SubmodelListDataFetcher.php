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

final class SubmodelListDataFetcher extends AbstractPersonalizationConsumer implements SubmodelListDataFetcherInterface
{
    private const ENDPOINT_URI = '/api/v3/%d/structure/get_submodel_list/%s';
    public const PARAM_SUBMODEL_LIST = 'submodelList';

    public function __construct(PersonalizationClientInterface $client, string $endPointUri)
    {
        parent::__construct($client, $endPointUri . self::ENDPOINT_URI);
    }

    public function fetchSubmodelList(
        int $customerId,
        string $licenseKey,
        string $referenceCode
    ): ResponseInterface {
        $uri = $this->buildEndPointUri(
            [
                $customerId,
                $referenceCode,
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

class_alias(SubmodelListDataFetcher::class, 'Ibexa\Platform\Personalization\Client\Consumer\Model\SubmodelListDataFetcher');
