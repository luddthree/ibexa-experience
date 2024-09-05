<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FieldTypePage\Calendar;

use DateTime;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException as APIUnauthorizedException;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\FieldTypePage\Persistence\BlockEntry;

class BlockVisibilityEntryBuilder
{
    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    /** @var \Ibexa\Contracts\Core\Repository\LanguageService */
    private $languageService;

    public function __construct(
        ContentService $contentService,
        UserService $userService,
        LanguageService $languageService
    ) {
        $this->contentService = $contentService;
        $this->userService = $userService;
        $this->languageService = $languageService;
    }

    public function build(BlockEntry $blockEntry): ?ScheduledVisibilityEntry
    {
        $user = $this->loadUser((int)$blockEntry->userId);
        $content = $this->loadContent((int)$blockEntry->contentId);

        if ($this->userExists($user) && $this->contentIsAccessible($content)) {
            return $this->buildEntry(
                $blockEntry,
                $user,
                $content
            );
        }

        return null;
    }

    private function loadUser(int $userId): ?User
    {
        try {
            return $this->userService->loadUser($userId);
        } catch (NotFoundException $e) {
            return null;
        }
    }

    private function loadContent(int $contentId): ?Content
    {
        try {
            return $this->contentService->loadContent($contentId);
        } catch (APIUnauthorizedException | NotFoundException $e) {
            return null;
        }
    }

    private function userExists(?User $user): bool
    {
        return null !== $user;
    }

    private function contentIsAccessible(?Content $content): bool
    {
        return null !== $content;
    }

    private function buildEntry(
        BlockEntry $blockEntry,
        User $user,
        Content $content
    ): ScheduledVisibilityEntry {
        return new ScheduledVisibilityEntry(
            $blockEntry->id,
            $this->getDate($blockEntry),
            $this->getLanguage($blockEntry),
            $user,
            $content,
            $blockEntry->blockName,
            $blockEntry->blockType
        );
    }

    private function getDate(BlockEntry $blockEntry): DateTime
    {
        return DateTime::createFromFormat('U', (string)$blockEntry->actionTimestamp);
    }

    private function getLanguage(BlockEntry $blockEntry): Language
    {
        $versionInfo = $this->contentService->loadVersionInfoById(
            (int)$blockEntry->contentId,
            (int)$blockEntry->versionNumber
        );

        return $this->languageService->loadLanguage(
            $versionInfo->initialLanguageCode
        );
    }
}

class_alias(BlockVisibilityEntryBuilder::class, 'EzSystems\EzPlatformPageFieldType\Calendar\BlockVisibilityEntryBuilder');
