<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client;

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

/**
 * @internal
 */
interface PersonalizationClientInterface
{
    /**
     * @return \Ibexa\Personalization\Client\PersonalizationClientInterface
     */
    public function setCustomerId(?int $customerId = null): self;

    public function getCustomerId(): ?int;

    /**
     * @return \Ibexa\Personalization\Client\PersonalizationClientInterface
     */
    public function setLicenseKey(?string $licenseKey = null): self;

    public function getLicenseKey(): ?string;

    /**
     * @return \Ibexa\Personalization\Client\PersonalizationClientInterface
     */
    public function setUserIdentifier(string $userIdentifier): self;

    public function getUserIdentifier(): ?string;

    /**
     * @param array<string, array|scalar|\JsonSerializable> $options
     */
    public function sendRequest(string $method, UriInterface $uri, array $options = []): ResponseInterface;

    public function getHttpClient(): ClientInterface;
}

class_alias(PersonalizationClientInterface::class, 'EzSystems\EzRecommendationClient\Client\EzRecommendationClientInterface');
