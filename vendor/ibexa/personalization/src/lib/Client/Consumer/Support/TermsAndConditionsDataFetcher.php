<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Support;

use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;

final class TermsAndConditionsDataFetcher extends AbstractSupportConsumer implements TermsAndConditionsDataFetcherInterface
{
    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fetchTermsAndConditions(): ResponseInterface
    {
        return $this->client->request(
            Request::METHOD_GET,
            $this->endpoint,
        );
    }
}

class_alias(TermsAndConditionsDataFetcher::class, 'Ibexa\Platform\Personalization\Client\Consumer\Support\TermsAndConditionsDataFetcher');
