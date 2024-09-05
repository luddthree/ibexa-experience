<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Item;

use Ibexa\Personalization\Client\Consumer\AbstractPersonalizationConsumer;
use Ibexa\Personalization\Client\PersonalizationClientInterface;
use Ibexa\Personalization\Exception\BadResponseException;
use Ibexa\Personalization\Exception\InvalidResponseStatusCodeException;
use Ibexa\Personalization\Exception\ItemRequestException;
use Ibexa\Personalization\Request\Item\ItemRequest;
use Ibexa\Personalization\Value\Authentication\Parameters;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 */
final class ItemDataSender extends AbstractPersonalizationConsumer implements ItemDataSenderInterface
{
    public const ENDPOINT_URI_SUFFIX = '/api/%d/items';

    private const ACTION_EXPORT = 'export';
    private const ACTION_UPDATE = 'update';
    private const ACTION_DELETE = 'delete';

    public function __construct(PersonalizationClientInterface $client, string $endPointUri)
    {
        parent::__construct($client, $endPointUri . self::ENDPOINT_URI_SUFFIX);
    }

    public function triggerExport(
        Parameters $parameters,
        ItemRequest $request,
        ?UriInterface $webHookUri = null
    ): ResponseInterface {
        return $this->sendRequest(
            $parameters,
            HttpRequest::METHOD_POST,
            $request,
            self::ACTION_EXPORT,
            $webHookUri
        );
    }

    public function triggerUpdate(
        Parameters $parameters,
        ItemRequest $request
    ): ResponseInterface {
        return $this->sendRequest(
            $parameters,
            HttpRequest::METHOD_PUT,
            $request,
            self::ACTION_UPDATE,
        );
    }

    public function triggerDelete(
        Parameters $parameters,
        ItemRequest $request
    ): ResponseInterface {
        return $this->sendRequest(
            $parameters,
            HttpRequest::METHOD_DELETE,
            $request,
            self::ACTION_DELETE,
        );
    }

    /**
     * @throws \Ibexa\Personalization\Exception\TransferException
     */
    private function sendRequest(
        Parameters $parameters,
        string $method,
        ItemRequest $request,
        string $actionName,
        ?UriInterface $webHookUri = null
    ): ResponseInterface {
        $customerId = $parameters->getCustomerId();

        $uri = $webHookUri ?? $this->buildEndPointUri([$customerId]);

        $this->setAuthenticationParameters($customerId, $parameters->getLicenseKey());

        $options = array_merge(
            $this->getOptions(),
            [
                'json' => $request,
            ],
        );

        try {
            $response = $this->client->sendRequest($method, $uri, $options);
            $responseStatusCode = $response->getStatusCode();

            if (Response::HTTP_ACCEPTED !== $responseStatusCode) {
                throw new InvalidResponseStatusCodeException(
                    Response::HTTP_ACCEPTED,
                    $responseStatusCode
                );
            }

            return $response;
        } catch (BadResponseException $exception) {
            if (Response::HTTP_BAD_REQUEST === $exception->getCode()) {
                throw new ItemRequestException(
                    $actionName,
                    $request,
                    $exception
                );
            }

            throw $exception;
        }
    }
}
