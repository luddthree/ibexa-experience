<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Dashboard\EventSubscriber;

use Ibexa\Contracts\Core\Repository\Events\Trash\BeforeTrashEvent;
use Ibexa\Contracts\Core\Repository\Exceptions\ForbiddenException;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Tests\Dashboard\PhpUnit\ConfigResolverMockTrait;
use Ibexa\Tests\Dashboard\PhpUnit\ContentItemOfContentTypeMockTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

final class DeleteDashboardSubscriberTest extends TestCase
{
    use ConfigResolverMockTrait;
    use ContentItemOfContentTypeMockTrait;

    private const PREDEFINED_DASHBOARD_REMOTE_ID = 'default_dashboard';

    private DeleteDashboardSubscriber $deleteDashboardSubscriber;

    /** @var \Symfony\Contracts\Translation\TranslatorInterface&\PHPUnit\Framework\MockObject\MockObject */
    private TranslatorInterface $translator;

    public function setUp(): void
    {
        $configResolver = $this->mockConfigResolverWithMap(
            [
                ['dashboard.default_dashboard_remote_id', null, null, self::PREDEFINED_DASHBOARD_REMOTE_ID],
            ]
        );
        $this->translator = $this->createMock(TranslatorInterface::class);

        $this->deleteDashboardSubscriber = new DeleteDashboardSubscriber(
            $configResolver,
            $this->translator,
        );
    }

    public function testGetSubscribedEvents(): void
    {
        self::assertEquals(
            [
                BeforeTrashEvent::class => ['onBeforeTrashEvent'],
            ],
            DeleteDashboardSubscriber::getSubscribedEvents()
        );
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     */
    public function testOnDeletePredefinedDashboard(): void
    {
        $event = $this->createEvent(
            $this->mockLocationOfPredefinedDashboardType()
        );

        $this->translator
            ->expects(self::once())
            ->method('trans')
            ->with(
                'dashboard.sent_to_trash.info',
                [],
                'ibexa_dashboard'
            )
            ->willReturn('The default dashboard cannot be sent to the trash');

        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage('The default dashboard cannot be sent to the trash');

        $this->deleteDashboardSubscriber->onBeforeTrashEvent($event);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     */
    public function testOnDeleteDashboard(): void
    {
        $event = $this->createEvent(
            $this->mockLocationOfContentItemOfDashboardType()
        );

        $this->translator
            ->expects(self::never())
            ->method('trans');

        $this->deleteDashboardSubscriber->onBeforeTrashEvent($event);
    }

    private function createEvent(
        Location $location
    ): BeforeTrashEvent {
        return new BeforeTrashEvent($location);
    }
}
