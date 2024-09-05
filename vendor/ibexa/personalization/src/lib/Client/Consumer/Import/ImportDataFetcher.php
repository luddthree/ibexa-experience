<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Import;

use Ibexa\Personalization\Client\Consumer\AbstractPersonalizationConsumer;
use Ibexa\Personalization\Client\PersonalizationClientInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;

final class ImportDataFetcher extends AbstractPersonalizationConsumer implements ImportDataFetcherInterface
{
    private const ENDPOINT_URI = '/api/v5/%d/import/metadata';

    public function __construct(PersonalizationClientInterface $client, string $endPointUri)
    {
        parent::__construct($client, $endPointUri . self::ENDPOINT_URI);
    }

    public function fetchImportedItems(int $customerId, string $licenseKey): ResponseInterface
    {
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

class_alias(ImportDataFetcher::class, 'Ibexa\Platform\Personalization\Client\Consumer\Import\ImportDataFetcher');
