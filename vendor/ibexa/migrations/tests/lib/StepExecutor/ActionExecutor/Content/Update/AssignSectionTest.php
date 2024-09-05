<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration\StepExecutor\ActionExecutor\Content\Update;

use Ibexa\Contracts\Core\Repository\SectionService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Section;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Migration\StepExecutor\ActionExecutor\Content\Update\AssignSectionExecutor;
use Ibexa\Migration\ValueObject\Step\Action;
use Ibexa\Migration\ValueObject\Step\Action\Content\AssignSection as AssignSectionAction;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Migration\StepExecutor\ActionExecutor\Content\Update\AssignSectionExecutor
 */
final class AssignSectionTest extends TestCase
{
    /** @var \Ibexa\Migration\StepExecutor\ActionExecutor\Content\Update\AssignSectionExecutor */
    private $executor;

    /** @var \Ibexa\Contracts\Core\Repository\SectionService|\PHPUnit\Framework\MockObject\MockObject */
    private $sectionService;

    protected function setUp(): void
    {
        $this->sectionService = $this->createMock(SectionService::class);

        $this->executor = new AssignSectionExecutor($this->sectionService);
    }

    public function testCanAssignSectionById(): void
    {
        $this->sectionService->expects(self::once())
            ->method('loadSection')
            ->willReturn(self::getSection());

        $this->sectionService->expects(self::once())
            ->method('assignSection')
            ->with(
                self::callback(static function (ContentInfo $contentInfo): bool {
                    self::assertSame(123456, $contentInfo->id);

                    return true;
                }),
                self::callback(static function (Section $section): bool {
                    self::assertSame(2, $section->id);

                    return true;
                })
            );

        $this->executor->handle(new AssignSectionAction(2, null), $this->getContent());
    }

    public function testCanAssignSectionByIdentifier(): void
    {
        $this->sectionService->expects(self::once())
            ->method('loadSectionByIdentifier')
            ->willReturn(self::getSection());

        $this->sectionService->expects(self::once())
            ->method('assignSection')
            ->with(
                self::callback(static function (ContentInfo $contentInfo): bool {
                    self::assertSame(123456, $contentInfo->id);

                    return true;
                }),
                self::callback(static function (Section $section): bool {
                    self::assertSame('Standard', $section->identifier);

                    return true;
                })
            );

        $this->executor->handle(new AssignSectionAction(null, 'Standard'), self::getContent());
    }

    public function testThrowsRuntimeExceptionWhenNonAssignToSection(): void
    {
        $action = $this->createMock(Action::class);

        $this->expectException(\InvalidArgumentException::class);
        $this->executor->handle($action, self::getContent());
    }

    public function testThrowsRuntimeExceptionWhenNonAssignSectionIsPassed(): void
    {
        $this->sectionService->expects(self::never())
            ->method('assignSection');

        $action = $this->createMock(Action::class);

        $this->expectException(\InvalidArgumentException::class);
        $this->executor->handle($action, self::getContent());
    }

    private static function getContent(): Content
    {
        return new Content(
            [
                'versionInfo' => new VersionInfo(
                    [
                        'contentInfo' => new ContentInfo(
                            ['id' => 123456]
                        ),
                    ]
                ),
                'internalFields' => [],
            ]
        );
    }

    private static function getSection(): Section
    {
        return new Section(
            [
                'id' => 2,
                'identifier' => 'Standard',
                'name' => 'Standard',
            ]
        );
    }
}
