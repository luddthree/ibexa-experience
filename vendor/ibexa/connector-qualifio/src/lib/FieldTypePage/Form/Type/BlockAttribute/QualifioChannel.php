<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ConnectorQualifio\FieldTypePage\Form\Type\BlockAttribute;

use DateTimeImmutable;
use Exception;

/**
 * @phpstan-import-type TChannelData from \Ibexa\Contracts\ConnectorQualifio\QualifioServiceInterface
 */
final class QualifioChannel
{
    private string $websiteName;

    private string $campaignTitle;

    private int $campaignId;

    private string $campaignType;

    private ?DateTimeImmutable $startDate = null;

    private ?DateTimeImmutable $endDate = null;

    public function __construct(int $campaignId)
    {
        $this->campaignId = $campaignId;
    }

    /**
     * @phpstan-param TChannelData $data
     */
    public static function createFromArray(array $data): self
    {
        $self = new self($data['campaign']['campaignId']);

        $self->websiteName = $data['website']['name'];
        $self->campaignTitle = $data['campaign']['campaignTitle'];
        $self->campaignType = $data['campaign']['campaignType'];
        $self->startDate = self::convertDate($data['schedule']['startDate'] ?? null);
        $self->endDate = self::convertDate($data['schedule']['endDate'] ?? null);

        return $self;
    }

    public function getWebsiteName(): string
    {
        return $this->websiteName;
    }

    public function getCampaignTitle(): string
    {
        return $this->campaignTitle;
    }

    public function getCampaignId(): int
    {
        return $this->campaignId;
    }

    public function getCampaignType(): string
    {
        return $this->campaignType;
    }

    public function getStartDate(): ?DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getEndDate(): ?DateTimeImmutable
    {
        return $this->endDate;
    }

    private static function convertDate(?string $data): ?DateTimeImmutable
    {
        try {
            if (null !== $data) {
                return new DateTimeImmutable($data);
            }
        } catch (Exception $e) {
        }

        return null;
    }
}
