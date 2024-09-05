<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ConnectorQualifio\Service;

final class QualifioConfiguration
{
    private ?int $clientId;

    private ?string $channel;

    private ?string $feedUrl;

    public function __construct(
        ?int $clientId,
        ?string $channel,
        ?string $feedUrl
    ) {
        $this->clientId = $clientId;
        $this->channel = $channel;
        $this->feedUrl = $feedUrl;
    }

    /**
     * @phpstan-assert-if-true !null $this->clientId
     * @phpstan-assert-if-true !null $this->channel
     * @phpstan-assert-if-true !null $this->feedUrl
     */
    public function isConfigured(): bool
    {
        return $this->clientId !== null && $this->channel !== null && $this->feedUrl !== null;
    }

    public function getChannel(): string
    {
        if ($this->channel === null) {
            $this->throwMissingConfigurationException();
        }

        return $this->channel;
    }

    public function getClientId(): int
    {
        if ($this->clientId === null) {
            $this->throwMissingConfigurationException();
        }

        return $this->clientId;
    }

    public function getFeedUrl(): string
    {
        if ($this->feedUrl === null) {
            $this->throwMissingConfigurationException();
        }

        return $this->feedUrl;
    }

    /**
     * @return never
     */
    private function throwMissingConfigurationException(): void
    {
        throw new \RuntimeException(sprintf(
            'Missing configuration for %s. Ensure that %s bundle is configured',
            self::class,
            'ibexa_connector_qualifio',
        ));
    }
}
