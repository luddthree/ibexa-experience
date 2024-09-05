<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Calendar;

use DateTimeInterface;
use Ibexa\Contracts\Calendar\Event;
use Ibexa\Contracts\Calendar\EventType\EventTypeInterface;
use Ibexa\Contracts\Calendar\LanguageBasedEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\User\User;

abstract class BlockVisibilityEvent extends Event implements LanguageBasedEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language */
    private $language;

    /** @var \Ibexa\Contracts\Core\Repository\Values\User\User */
    private $user;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content */
    private $content;

    /** @var string */
    private $blockName;

    /** @var string */
    private $blockType;

    public function __construct(
        EventTypeInterface $type,
        string $id,
        DateTimeInterface $date,
        Language $language,
        User $user,
        Content $content,
        string $blockName,
        string $blockType
    ) {
        parent::__construct($type, $id, $date);

        $this->language = $language;
        $this->user = $user;
        $this->content = $content;
        $this->blockName = $blockName;
        $this->blockType = $blockType;
    }

    public function getLanguage(): Language
    {
        return $this->language;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getContent(): Content
    {
        return $this->content;
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

class_alias(BlockVisibilityEvent::class, 'EzSystems\EzPlatformPageFieldType\Calendar\BlockVisibilityEvent');
