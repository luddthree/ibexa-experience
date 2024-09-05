<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ConnectorQualifio\Service;

use Ibexa\Contracts\ConnectorQualifio\QualifioServiceInterface;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @phpstan-import-type TChannelData from \Ibexa\Contracts\ConnectorQualifio\QualifioServiceInterface
 */
final class QualifioService implements QualifioServiceInterface
{
    private const ISSUER = 'Ibexa DXP';

    private const USER_PREFIX = 'ibexa_user_';

    private const QUALIFIO_TOKEN_IDENTIFIER = 'qual_token';

    private const QUALIFIO_FEED_FORMAT = 'json';

    private PermissionResolver $permissionResolver;

    private ConfigResolverInterface $configResolver;

    private Security $security;

    private HttpClientInterface $httpClient;

    private JWTManager $tokenManager;

    /** @var iterable<\Ibexa\ConnectorQualifio\Security\JWT\Token\TokenEnricherInterface> */
    private iterable $tokenEnrichers;

    private QualifioConfiguration $configuration;

    /**
     * @param iterable<\Ibexa\ConnectorQualifio\Security\JWT\Token\TokenEnricherInterface> $tokenEnrichers
     */
    public function __construct(
        QualifioConfiguration $configuration,
        Security $security,
        PermissionResolver $permissionResolver,
        ConfigResolverInterface $configResolver,
        HttpClientInterface $httpClient,
        JWTManager $tokenManager,
        iterable $tokenEnrichers
    ) {
        $this->configuration = $configuration;
        $this->permissionResolver = $permissionResolver;
        $this->configResolver = $configResolver;
        $this->tokenManager = $tokenManager;
        $this->security = $security;
        $this->httpClient = $httpClient;
        $this->tokenEnrichers = $tokenEnrichers;
    }

    public function isConfigured(): bool
    {
        return $this->configuration->isConfigured();
    }

    public function getUserToken(int $campaignId): ?string
    {
        if ($this->isAnonymous()) {
            return null;
        }

        $user = $this->security->getUser();
        if ($user === null) {
            return null;
        }

        $userId = $this->getCurrentUserId();
        $tokenPayload = [
            'sub' => self::USER_PREFIX . $userId,
            'iss' => self::ISSUER,
            'campaignId' => $campaignId,
        ];

        foreach ($this->tokenEnrichers as $tokenEnricher) {
            $payload = $tokenEnricher->getPayload($userId);
            $tokenPayload = array_merge_recursive($tokenPayload, $payload);
        }

        return $this->tokenManager->createFromPayload($user, $tokenPayload);
    }

    public function getQualifioChannels(): array
    {
        $channels = [];
        $this->collectChannels($channels, $this->configuration->getChannel(), $this->configuration->getClientId());

        usort($channels, static function (array $channelA, array $channelB): int {
            $startDateA = null;
            if (isset($channelA['schedule']['startDate'])) {
                $startDateA = strtotime($channelA['schedule']['startDate']) ?: null;
            }

            $startDateB = null;
            if (isset($channelB['schedule']['startDate'])) {
                $startDateB = strtotime($channelB['schedule']['startDate']) ?: null;
            }

            return $startDateA <=> $startDateB;
        });

        return $channels;
    }

    public function getQualifioCampaign(int $campaignId): ?array
    {
        $feedResult = $this->makeApiRequest($this->configuration->getChannel(), $this->configuration->getClientId(), $campaignId);

        // API does not always respond with cursor as first entry on multiple channel campaigns
        if (isset($feedResult['numberRows'], $feedResult['channels']) && $feedResult['numberRows'] !== 0) {
            foreach ($feedResult['channels'] as $channelData) {
                if (($channelData['campaign']['campaignId'] == $campaignId) && $this->validateChannelRuntime($channelData)) {
                    return $channelData;
                }
            }
        }

        return null;
    }

    /**
     * Recursive API query function with cursor.
     *
     * @phpstan-param array<string|int, TChannelData> $channels
     */
    private function collectChannels(array &$channels, string $channel, int $clientId, int $cursor = null): void
    {
        $lastCursor = null;
        $feedResult = $this->makeApiRequest($channel, $clientId, $cursor);

        if (isset($feedResult['channels'], $feedResult['numberRows'])) {
            foreach ($feedResult['channels'] as $channelData) {
                $campaignId = $channelData['campaign']['campaignId'];
                if (!isset($channels[$campaignId]) && $this->validateChannelRuntime($channelData)) {
                    $channels[$campaignId] = $channelData;
                }

                $lastCursor = $campaignId;
            }
        }

        if ($lastCursor !== $cursor) {
            //query for more campaigns with last channel id, API does  not have real offset / limit function
            $this->collectChannels($channels, $channel, $clientId, $lastCursor);
        }
    }

    /**
     * @phpstan-param TChannelData $channelData
     */
    private function validateChannelRuntime(array $channelData): bool
    {
        if (!isset($channelData['schedule']['endDate'])) {
            return true;
        }

        //verify if official campaign "end date" is in the future (i.e. campaign has not ended yet)
        return strtotime($channelData['schedule']['endDate']) >= time();
    }

    /**
     * @phpstan-return array{
     *     numberRows?: int,
     *     channels?: array<TChannelData>,
     * }
     */
    private function makeApiRequest(string $channel, int $clientId, int $cursor = null): array
    {
        $queryParameters = [
            'clientId' => $clientId,
        ];

        if ($cursor) {
            $queryParameters['cursor'] = $cursor;
        }

        $response = $this->httpClient->request(
            'GET',
            $this->configuration->getFeedUrl() . '/' . $channel . '/' . self::QUALIFIO_FEED_FORMAT,
            [
                'query' => $queryParameters,
            ],
        );

        return $response->toArray();
    }

    public function buildCampaignUrl(int $campaignId): ?string
    {
        $campaign = $this->getQualifioCampaign($campaignId);

        if (!$campaign) {
            return null;
        }

        $campaignUrl = $campaign['integration']['webview'];

        if ($token = $this->getUserToken($campaignId)) {
            return $this->addQueryParameters(
                $campaignUrl,
                [
                    QualifioService::QUALIFIO_TOKEN_IDENTIFIER => $token,
                ]
            );
        }

        return $campaignUrl;
    }

    /**
     * @param array<mixed> $parameters
     */
    private function addQueryParameters(string $url, array $parameters): string
    {
        // No access to http_build_url, this requires PECL pecl_http extension
        $query = parse_url($url, PHP_URL_QUERY);

        if ($query) {
            $url = $url . '&' . http_build_query($parameters);
        } else {
            $url = $url . '?' . http_build_query($parameters);
        }

        return $url;
    }

    private function getAnonymousId(): int
    {
        return (int) $this->configResolver->getParameter('anonymous_user_id');
    }

    private function getCurrentUserId(): int
    {
        return $this->permissionResolver->getCurrentUserReference()->getUserId();
    }

    private function isAnonymous(): bool
    {
        return $this->getCurrentUserId() === $this->getAnonymousId();
    }
}
