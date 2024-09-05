<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Connect\PageBuilder;

use Ibexa\Bundle\Connect\Event\PrePageBlockWebhookRequestEvent;
use Ibexa\Bundle\Connect\EventSubscriber\PageBuilderPreRenderEventSubscriber;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use InvalidArgumentException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class BlockClient implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private HttpClientInterface $httpClient;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        HttpClientInterface $httpClient,
        EventDispatcherInterface $eventDispatcher,
        ?LoggerInterface $logger = null
    ) {
        $this->httpClient = $httpClient;
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @return array<mixed>|null
     */
    public function getExtraBlockData(BlockValue $blockValue): ?array
    {
        $url = $this->getUrl($blockValue);
        if ($url === null) {
            return null;
        }

        try {
            $event = new PrePageBlockWebhookRequestEvent($blockValue);
            $this->eventDispatcher->dispatch($event);

            return $this->doRequest($url, $blockValue, $event);
        } catch (ServerExceptionInterface $e) {
            $this->handleServerError($e, $blockValue);
        } catch (ExceptionInterface $e) {
            $this->handleTransportError($e, $blockValue);
        }

        return null;
    }

    /**
     * @return array<mixed>|null
     *
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    private function doRequest(
        string $url,
        BlockValue $blockValue,
        PrePageBlockWebhookRequestEvent $event
    ): ?array {
        $response = $this->httpClient->request($event->getRequestMethod(), $url, [
            'query' => $event->getQuery(),
            'json' => $event->getPayload(),
        ]);

        // "Accepted" is valid response from a webhook when it is not running.
        // In this case request is put into queue and handled later, when webhook scenario is enabled.
        // We treat it as an error, because our block relies on a proper response.
        if ($response->getContent() === 'Accepted') {
            $message = $this->getLogMessage(
                $blockValue,
                'Webhook is not currently listening for requests in Ibexa Connect',
            );
            $this->logger->error($message, [
                'block' => $blockValue,
            ]);

            return null;
        }

        return $response->toArray();
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function getUrl(BlockValue $blockValue): ?string
    {
        $url = $blockValue->getAttribute('url');
        if ($url === null) {
            $message = $this->getLogMessage(
                $blockValue,
                sprintf(
                    'Expected to have "%s" attribute set. Block rendered without performing HTTP request',
                    'url',
                ),
            );
            $this->logger->warning($message, [
                'block' => $blockValue,
            ]);

            return null;
        }

        $urlValue = $url->getValue();
        if (!is_string($urlValue)) {
            $message = $this->getLogMessage(
                $blockValue,
                sprintf(
                    '"%s" block attribute is expected to be of type string, received %s instead',
                    'url',
                    get_debug_type($urlValue),
                ),
            );
            throw new InvalidArgumentException($message);
        }

        return $urlValue;
    }

    private function handleServerError(
        ServerExceptionInterface $e,
        BlockValue $blockValue
    ): void {
        $content = $e->getResponse()->getContent(false);
        $message = $this->getLogMessage(
            $blockValue,
            sprintf(
                'Ibexa Connect server responded with an error: %s (HTTP code: %d)',
                $content,
                $e->getCode(),
            ),
        );
        $this->logger->error($message, [
            'exception' => $e,
            'block' => $blockValue,
        ]);
    }

    private function handleTransportError(
        ExceptionInterface $e,
        BlockValue $blockValue
    ): void {
        $message = $this->getLogMessage(
            $blockValue,
            sprintf('HTTP request processing ended with an error: %s', $e->getMessage()),
        );
        $this->logger->error($message, [
            'exception' => $e,
            'block' => $blockValue,
        ]);
    }

    private function getLogMessage(BlockValue $blockValue, string $message): string
    {
        return sprintf(
            'Rendering of Page Builder block "%s" of type "%s" failed. %s.',
            $blockValue->getName() ?? $blockValue->getId(),
            PageBuilderPreRenderEventSubscriber::IBEXA_CONNECT_BLOCK,
            $message,
        );
    }
}
