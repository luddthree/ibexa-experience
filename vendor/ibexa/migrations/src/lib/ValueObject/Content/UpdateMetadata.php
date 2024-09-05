<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Content;

use DateTime;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;

final class UpdateMetadata
{
    /** @var string|null */
    public $initialLanguageCode;

    /** @var int|null */
    public $creatorId;

    /** @var string|null */
    public $remoteId;

    /** @var bool|null */
    public $alwaysAvailable;

    /** @var string|null */
    public $mainLanguageCode;

    /** @var mixed|null */
    public $mainLocationId;

    /** @var \DateTime|null */
    public $modificationDate;

    /** @var \DateTime|null */
    public $publishedDate;

    /** @var string|null */
    public $name;

    /** @var mixed|null */
    public $ownerId;

    /**
     * @param mixed|null $mainLocationId
     * @param mixed|null $ownerId
     */
    private function __construct(
        ?string $initialLanguageCode,
        ?int $creatorId,
        ?string $remoteId,
        ?bool $alwaysAvailable,
        ?string $mainLanguageCode,
        $mainLocationId,
        ?DateTime $modificationDate,
        ?string $name,
        $ownerId,
        ?DateTime $publishedDate
    ) {
        $this->initialLanguageCode = $initialLanguageCode;
        $this->creatorId = $creatorId;
        $this->remoteId = $remoteId;
        $this->alwaysAvailable = $alwaysAvailable;
        $this->mainLanguageCode = $mainLanguageCode;
        $this->mainLocationId = $mainLocationId;
        $this->modificationDate = $modificationDate;
        $this->name = $name;
        $this->ownerId = $ownerId;
        $this->publishedDate = $publishedDate;
    }

    public static function createFromContent(Content $content): self
    {
        $contentInfo = $content->contentInfo;

        return new self(
            $content->getVersionInfo()->initialLanguageCode,
            $contentInfo->ownerId,
            $contentInfo->remoteId,
            $contentInfo->alwaysAvailable,
            $contentInfo->mainLanguageCode,
            $contentInfo->mainLocationId,
            $contentInfo->modificationDate,
            $contentInfo->name,
            $contentInfo->ownerId,
            $contentInfo->publishedDate
        );
    }

    public static function createFromArray(array $data): self
    {
        return new self(
            $data['initialLanguageCode'] ?? null,
            $data['creatorId'] ?? null,
            $data['remoteId'] ?? null,
            $data['alwaysAvailable'] ?? null,
            $data['mainLanguageCode'] ?? null,
            $data['mainLocationId'] ?? null,
            isset($data['modificationDate']) ? new DateTime($data['modificationDate']) : null,
            $data['name'] ?? null,
            $data['ownerId'] ?? null,
            isset($data['publishedDate']) ? new DateTime($data['publishedDate']) : null
        );
    }

    public function requiresContentMetadataUpdate(): bool
    {
        return $this->remoteId !== null
            || $this->alwaysAvailable !== null
            || $this->mainLanguageCode !== null
            || $this->mainLocationId !== null
            || $this->modificationDate !== null
            || $this->name !== null
            || $this->ownerId !== null
            || $this->publishedDate !== null;
    }
}

class_alias(UpdateMetadata::class, 'Ibexa\Platform\Migration\ValueObject\Content\UpdateMetadata');
