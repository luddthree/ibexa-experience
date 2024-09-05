<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FieldTypePage\Calendar;

use DateTimeInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\User\User;

final class ScheduledVisibilityEntry implements ScheduledEntryInterface
{
    /** @var int */
    private $id;

    /** @var \Ibexa\Contracts\Core\Repository\Values\User\User */
    private $user;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content */
    private $content;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language */
    private $language;

    /** @var int */
    private $versionNumber;

    /** @var \DateTimeInterface */
    private $date;

    /** @var string */
    private $blockName;

    /** @var string */
    private $blockType;

    public function __construct(
        string $id,
        DateTimeInterface $date,
        Language $language,
        User $user,
        Content $content,
        string $blockName,
        string $blockType
    ) {
        $this->id = $id;
        $this->date = $date;
        $this->language = $language;
        $this->user = $user;
        $this->content = $content;
        $this->blockName = $blockName;
        $this->blockType = $blockType;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getContent(): Content
    {
        return $this->content;
    }

    public function getLanguage(): Language
    {
        return $this->language;
    }

    public function getVersionNumber(): int
    {
        return $this->versionNumber;
    }

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    public function getBlockName(): string
    {
        return $this->blockName;
    }

    public function getBlockType(): string
    {
        return $this->blockType;
    }
}

class_alias(ScheduledVisibilityEntry::class, 'EzSystems\EzPlatformPageFieldType\Calendar\ScheduledVisibilityEntry');
