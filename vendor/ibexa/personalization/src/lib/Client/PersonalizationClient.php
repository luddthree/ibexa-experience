<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException as GuzzleBadResponseException;
use Ibexa\Personalization\Config\CredentialsResolverInterface;
use Ibexa\Personalization\Exception\BadResponseException;
use Ibexa\Personalization\Exception\CredentialsNotFoundException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class PersonalizationClient implements PersonalizationClientInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private const ERROR_MESSAGE = 'ClientError: ';

    private ClientInterface $client;

    private CredentialsResolverInterface $credentialsResolver;

    private ?int $customerId = null;

    private ?string $licenseKey = null;

    private ?string $userIdentifier = null;

    public function __construct(
        ClientInterface $client,
        CredentialsResolverInterface $credentialsResolver,
        ?LoggerInterface $logger = null
    ) {
        $this->client = $client;
        $this->credentialsResolver = $credentialsResolver;
        $this->logger = $logger ?? new NullLogger();

        if ($this->credentialsResolver->hasCredentials()) {
            $this->setClientCredentials();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerId(?int $customerId = null): PersonalizationClientInterface
    {
        $this->customerId = $customerId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerId(): ?int
    {
        return $this->customerId;
    }

    /**
     * {@inheritdoc}
     */
    public function setLicenseKey(?string $licenseKey = null): PersonalizationClientInterface
    {
        $this->licenseKey = $licenseKey;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLicenseKey(): ?string
    {
        return $this->licenseKey;
    }

    /**
     * {@inheritdoc}
     */
    public function setUserIdentifier(string $userIdentifier): PersonalizationClientInterface
    {
        $this->userIdentifier = $userIdentifier;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserIdentifier(): ?string
    {
        return $this->userIdentifier;
    }

    /**
     * @throws \Ibexa\Personalization\Exception\BadResponseException
     */
    public function sendRequest(string $method, UriInterface $uri, array $options = []): ResponseInterface
    {
        try {
            if (!$this->hasCredentials()) {
                $this->logger->warning(self::ERROR_MESSAGE . 'Recommendation credentials are not set', []);

                throw new CredentialsNotFoundException();
            }

            return $this->getHttpClient()->request($method, $uri, array_merge($options, [
                'auth' => [
                    $this->getCustomerId(),
                    $this->getLicenseKey(),
                ],
            ]));
        } catch (GuzzleBadResponseException $exception) {
            $this->logger->error(
                sprintf(
                    self::ERROR_MESSAGE . 'Error while sending data: %s %s %s %s',
                    $exception->getMessage(),
                    $exception->getCode(),
                    $exception->getFile(),
                    $exception->getLine()
                ),
                [
                    'exception' => $exception,
                ]
            );

            throw new BadResponseException(
                $exception->getMessage(),
                $exception->getRequest(),
                $exception->getResponse(),
                $exception->getPrevious(),
                $exception->getHandlerContext()
            );
        }
    }

    /**
     * Checks if client has set Recommendation credentials.
     */
    public function hasCredentials(): bool
    {
        return !empty($this->getCustomerId()) && !empty($this->getLicenseKey());
    }

    /**
     * {@inheritdoc}
     */
    public function getHttpClient(): ClientInterface
    {
        return $this->client;
    }

    /**
     * Sets client credentials from CredentialsResolver.
     */
    private function setClientCredentials(): void
    {
        /** @var \Ibexa\Personalization\Value\Config\PersonalizationClientCredentials $credentials */
        $credentials = $this->credentialsResolver->getCredentials();

        if (null !== $credentials) {
            $this
                ->setCustomerId($credentials->getCustomerId())
                ->setLicenseKey($credentials->getLicenseKey());
        }
    }
}

class_alias(PersonalizationClient::class, 'EzSystems\EzRecommendationClient\Client\EzRecommendationClient');
