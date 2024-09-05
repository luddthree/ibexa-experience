<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer;

use GuzzleHttp\Psr7\Uri;
use Ibexa\Personalization\Client\PersonalizationClientInterface;
use Psr\Http\Message\UriInterface;

abstract class AbstractPersonalizationConsumer
{
    public const DEFAULT_CONTENT_TYPE = 'application/json';
    public const DATE_TIME_FORMAT = 'Y-m-d H:i:s';
    public const DATE_TIME_SEPARATED_FORMAT = DATE_ATOM;

    protected PersonalizationClientInterface $client;

    protected string $endPointUri;

    /** @var array<string, array<string, string>> */
    private array $options;

    public function __construct(PersonalizationClientInterface $client, string $endPointUri)
    {
        $this->client = $client;
        $this->endPointUri = $endPointUri;
    }

    protected function getEndPointUri(): UriInterface
    {
        return new Uri($this->endPointUri);
    }

    /**
     * @param array<array-key, int|string|null> $endPointParameters
     */
    protected function buildEndPointUri(array $endPointParameters, ?string $rawEndPointUri = null): UriInterface
    {
        if (empty($endPointParameters)) {
            return $this->getEndPointUri();
        }

        if ($rawEndPointUri) {
            return new Uri(vsprintf($rawEndPointUri, $endPointParameters));
        }

        return new Uri(vsprintf($this->endPointUri, $endPointParameters));
    }

    /**
     * @param array<string, array<string, string>> $options
     */
    protected function setOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * @return array<string, array<string, string>>
     */
    protected function getOptions(): array
    {
        if (empty($this->options)) {
            $this->setDefaultOptions();
        }

        return $this->options;
    }

    protected function setAuthenticationParameters(int $customerId, string $licenseKey): void
    {
        $this->client
            ->setCustomerId($customerId)
            ->setLicenseKey($licenseKey);
    }

    private function setDefaultOptions(): void
    {
        $this->options = [
            'headers' => [
                'Content-Type' => self::DEFAULT_CONTENT_TYPE,
            ],
        ];
    }
}

class_alias(AbstractPersonalizationConsumer::class, 'Ibexa\Platform\Personalization\Client\Consumer\AbstractPersonalizationConsumer');
