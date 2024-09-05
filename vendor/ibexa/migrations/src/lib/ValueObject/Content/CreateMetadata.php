<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Content;

use DateTime;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Migration\ValueObject\Content\Metadata\Section;

final class CreateMetadata
{
    /** @var string */
    public $contentType;

    /** @var string */
    public $mainTranslation;

    /** @var int|null */
    public $creatorId;

    /** @var \DateTime|null */
    public $modificationDate;

    /** @var \DateTime|null */
    public $publicationDate;

    /** @var string|null */
    public $remoteId;

    /** @var bool|null */
    public $alwaysAvailable;

    /** @var \Ibexa\Migration\ValueObject\Content\Metadata\Section|null */
    public $section;

    public function __construct(
        string $contentType,
        string $mainTranslation,
        ?int $creatorId = null,
        ?DateTime $modificationDate = null,
        ?DateTime $publicationDate = null,
        ?string $remoteId = null,
        ?bool $alwaysAvailable = null,
        ?Section $section = null
    ) {
        $this->contentType = $contentType;
        $this->mainTranslation = $mainTranslation;
        $this->creatorId = $creatorId;
        $this->modificationDate = $modificationDate;
        $this->publicationDate = $publicationDate;
        $this->remoteId = $remoteId;
        $this->alwaysAvailable = $alwaysAvailable;
        $this->section = $section;
    }

    public static function createFromContent(Content $content): self
    {
        $section = new Section(
            $content->contentInfo->getSection()->id,
            $content->contentInfo->getSection()->identifier
        );

        return new self(
            $content->getContentType()->identifier,
            $content->contentInfo->getMainLanguage()->languageCode,
            $content->contentInfo->ownerId,
            $content->contentInfo->modificationDate,
            $content->contentInfo->publishedDate,
            $content->contentInfo->remoteId,
            $content->contentInfo->alwaysAvailable,
            $section,
        );
    }
}

class_alias(CreateMetadata::class, 'Ibexa\Platform\Migration\ValueObject\Content\CreateMetadata');
