<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\UserGroup;

use DateTime;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;

final class CreateMetadata
{
    /** @var bool|null */
    public $alwaysAvailable;

    /** @var string|null */
    public $contentTypeIdentifier;

    /** @var string */
    public $mainLanguage;

    /** @var \DateTime|null */
    public $modificationDate;

    /** @var int|null */
    public $ownerId;

    /** @var int */
    public $parentGroupId;

    /** @var string|null */
    public $remoteId;

    /** @var int|null */
    public $sectionId;

    public function __construct(
        ?bool $alwaysAvailable,
        ?string $contentTypeIdentifier,
        string $mainLanguage,
        ?DateTime $modificationDate,
        ?int $ownerId,
        int $parentGroupId,
        ?string $remoteId,
        ?int $sectionId
    ) {
        $this->alwaysAvailable = $alwaysAvailable;
        $this->contentTypeIdentifier = $contentTypeIdentifier;
        $this->mainLanguage = $mainLanguage;
        $this->modificationDate = $modificationDate;
        $this->ownerId = $ownerId;
        $this->parentGroupId = $parentGroupId;
        $this->remoteId = $remoteId;
        $this->sectionId = $sectionId;
    }

    /**
     * @phpstan-param array{
     *     alwaysAvailable?: ?bool,
     *     contentTypeIdentifier?: ?string,
     *     mainLanguage: string,
     *     modificationDate?: ?string,
     *     ownerId?: ?int,
     *     parentGroupId: int,
     *     remoteId?: ?string,
     *     sectionId?: ?int,
     * } $data
     */
    public static function createFromArray(array $data): self
    {
        return new self(
            $data['alwaysAvailable'] ?? null,
            $data['contentTypeIdentifier'] ?? null,
            $data['mainLanguage'],
            isset($data['modificationDate']) ? new DateTime($data['modificationDate']) : null,
            $data['ownerId'] ?? null,
            $data['parentGroupId'],
            $data['remoteId'] ?? null,
            $data['sectionId'] ?? null
        );
    }

    public static function createFromApi(UserGroup $userGroup): self
    {
        return new self(
            $userGroup->contentInfo->alwaysAvailable,
            $userGroup->getContentType()->identifier,
            $userGroup->contentInfo->mainLanguageCode,
            clone $userGroup->contentInfo->modificationDate,
            $userGroup->contentInfo->ownerId,
            $userGroup->parentId,
            $userGroup->contentInfo->remoteId,
            $userGroup->contentInfo->sectionId
        );
    }
}

class_alias(CreateMetadata::class, 'Ibexa\Platform\Migration\ValueObject\UserGroup\CreateMetadata');
