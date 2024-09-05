<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\FieldTypePage\Calendar;

use DateTime;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Core\Base\Exceptions\UnauthorizedException;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Core\Repository\Values\User\User;
use Ibexa\FieldTypePage\Calendar\BlockVisibilityEntryBuilder;
use Ibexa\FieldTypePage\Calendar\ScheduledVisibilityEntry;
use Ibexa\FieldTypePage\Persistence\BlockEntry;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class BlockVisibilityEntryBuilderTest extends TestCase
{
    private const ID = 10;
    private const USER_ID = 20;
    private const CONTENT_ID = 30;
    private const VERSION_NUMBER = 40;
    private const ACTION_TIMESTAMP = 1602670804;
    private const BLOCK_NAME = '__BLOCK_NAME__';
    private const BLOCK_TYPE = '__BLOCK_TYPE__';

    private const INITIAL_LANGUAGE_CODE = 'eng-GB';

    /**
     * @var \Ibexa\FieldTypePage\Calendar\BlockVisibilityEntryBuilder
     */
    private $blockVisibilityEntryBuilder;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    /** @var \Ibexa\Contracts\Core\Repository\LanguageService */
    private $languageService;

    /** @var \Ibexa\Core\Repository\Values\User\User */
    private $user;

    /** @var \Ibexa\Core\Repository\Values\Content\Content */
    private $content;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language */
    private $language;

    protected function setUp(): void
    {
        $this->user = new User();
        $this->content = new Content();
        $this->language = new Language();

        $this->contentService = $this->createMock(ContentService::class);
        $this->userService = $this->createMock(UserService::class);
        $this->languageService = $this->createMock(LanguageService::class);

        $this->blockVisibilityEntryBuilder = new BlockVisibilityEntryBuilder(
            $this->contentService,
            $this->userService,
            $this->languageService
        );
    }

    public function testBuildWhenUserExistsAndContentIsAccessible()
    {
        $this->userService
            ->method('loadUser')
            ->with($this->equalTo(self::USER_ID))
            ->willReturn($this->user);

        $this->contentService
            ->method('loadContent')
            ->with($this->equalTo(self::CONTENT_ID))
            ->willReturn($this->content);
        $this->contentService
            ->method('loadVersionInfoById')
            ->with(
                $this->equalTo(self::CONTENT_ID),
                $this->equalTo(self::VERSION_NUMBER)
            )
            ->willReturn(new VersionInfo([
                'initialLanguageCode' => self::INITIAL_LANGUAGE_CODE,
            ]));

        $this->languageService
            ->method('loadLanguage')
            ->with($this->equalTo(self::INITIAL_LANGUAGE_CODE))
            ->willReturn($this->language);

        $blockEntry = $this->createBlockEntry();

        $expectedEntry = $this->createExpectedScheduledVisibilityEntry();
        $builtEntry = $this->blockVisibilityEntryBuilder->build($blockEntry);

        Assert::assertEquals($expectedEntry, $builtEntry);
    }

    public function providerForTestBuildWhenContentIsNotAccessible(): iterable
    {
        return [
            [
                new NotFoundException('__MESSAGE_', '__ID__'),
            ], [
                new UnauthorizedException('__MODULE__', '__FUNCTION__'),
            ],
        ];
    }

    /**
     * @dataProvider providerForTestBuildWhenContentIsNotAccessible
     */
    public function testBuildWhenContentIsNotAccessible(\Exception $exception)
    {
        $this->contentService
            ->method('loadContent')
            ->with($this->equalTo(self::CONTENT_ID))
            ->willThrowException($exception);

        $this->userService
            ->method('loadUser')
            ->with($this->equalTo(self::USER_ID))
            ->willReturn($this->user);

        $blockEntry = $this->createBlockEntry();

        $builtEntry = $this->blockVisibilityEntryBuilder->build($blockEntry);

        Assert::assertNull($builtEntry);
    }

    public function testBuildWhenUserDoesNorExist()
    {
        $this->contentService
            ->method('loadContent')
            ->with($this->equalTo(self::CONTENT_ID))
            ->willReturn($this->content);

        $this->userService
            ->method('loadUser')
            ->with($this->equalTo(self::USER_ID))
            ->willThrowException(new NotFoundException('__MESSAGE_', '__ID__'));

        $blockEntry = $this->createBlockEntry();

        $builtEntry = $this->blockVisibilityEntryBuilder->build($blockEntry);

        Assert::assertNull($builtEntry);
    }

    private function createBlockEntry(): BlockEntry
    {
        return new BlockEntry(
            [
                'id' => self::ID,
                'userId' => self::USER_ID,
                'contentId' => self::CONTENT_ID,
                'versionNumber' => self::VERSION_NUMBER,
                'actionTimestamp' => self::ACTION_TIMESTAMP,
                'blockName' => self::BLOCK_NAME,
                'blockType' => self::BLOCK_TYPE,
            ]
        );
    }

    private function createExpectedScheduledVisibilityEntry(): ScheduledVisibilityEntry
    {
        return new ScheduledVisibilityEntry(
            (string)self::ID,
            DateTime::createFromFormat('U', (string)self::ACTION_TIMESTAMP),
            $this->language,
            $this->user,
            $this->content,
            self::BLOCK_NAME,
            self::BLOCK_TYPE
        );
    }
}

class_alias(BlockVisibilityEntryBuilderTest::class, 'EzSystems\EzPlatformPageFieldType\Tests\Calendar\BlockVisibilityEntryBuilderTest');
