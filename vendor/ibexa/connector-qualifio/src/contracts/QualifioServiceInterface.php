<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ConnectorQualifio;

/**
 * @phpstan-type TChannelData array{
 *     channelId: string,
 *     website: array{
 *         id: int,
 *         name: string,
 *     },
 *     channel: string,
 *     id: int,
 *     dateCreated: string,
 *     dateLastModified: string,
 *     campaign: array{
 *         campaignId: int,
 *         campaignTitle: string,
 *         campaignType: string,
 *         creator: string,
 *     },
 *     schedule?: array{
 *         endDate?: string,
 *         startDate?: string,
 *         hourlyLimitation: bool,
 *     },
 *     integration: array{
 *         javascript: string,
 *         webview: string,
 *         html: string,
 *     },
 * }
 */
interface QualifioServiceInterface
{
    public function isConfigured(): bool;

    public function getUserToken(int $campaignId): ?string;

    /**
     * @phpstan-return TChannelData[]
     */
    public function getQualifioChannels(): array;

    /**
     * @phpstan-return TChannelData|null
     */
    public function getQualifioCampaign(int $campaignId): ?array;

    public function buildCampaignUrl(int $campaignId): ?string;
}
