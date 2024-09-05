<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\CorporateAccount\Security;

use Ibexa\Bundle\CorporateAccount\EventSubscriber\CorporatePortal\ChangePasswordSuccessViewSubscriber;
use Ibexa\Bundle\CorporateAccount\IbexaCorporateAccountBundle;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Core\MVC\Symfony\SiteAccessGroup;
use Ibexa\User\View\ChangePassword\SuccessView;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ChangePasswordSuccessViewSubscriberTest extends TestCase
{
    /** @var \Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $siteAccessServiceMock;

    /** @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $urlGeneratorMock;

    /** @var \Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $notificationHandlerMock;

    private ChangePasswordSuccessViewSubscriber $viewSubscriber;

    protected function setUp(): void
    {
        $this->siteAccessServiceMock = $this->createMock(SiteAccessServiceInterface::class);
        $this->urlGeneratorMock = $this->createMock(UrlGeneratorInterface::class);

        $this
            ->urlGeneratorMock
            ->method('generate')
            ->willReturn('some.path.to.redirect');

        $this->notificationHandlerMock = $this->createMock(TranslatableNotificationHandlerInterface::class);
        $this->viewSubscriber = new ChangePasswordSuccessViewSubscriber(
            $this->siteAccessServiceMock,
            $this->urlGeneratorMock,
            $this->notificationHandlerMock
        );
    }

    private function mockSiteAccessService(string $siteAccessName, string $siteAccessGroup): void
    {
        $this
            ->siteAccessServiceMock
            ->method('getCurrent')
            ->willReturn(new SiteAccess($siteAccessName));

        $siteAccessWithGroups = new SiteAccess($siteAccessName);
        $siteAccessWithGroups->groups[] = new SiteAccessGroup($siteAccessGroup);
        $this
            ->siteAccessServiceMock
            ->method('get')
            ->willReturn($siteAccessWithGroups);
    }

    /**
     * @param mixed $controllerResult
     */
    private function getViewEvent($controllerResult): ViewEvent
    {
        return new ViewEvent(
            $this->createMock(KernelInterface::class),
            $this->createMock(Request::class),
            HttpKernelInterface::MAIN_REQUEST,
            $controllerResult
        );
    }

    public function testRedirectInCustomerPortal(): void
    {
        $this->mockSiteAccessService('corporate', IbexaCorporateAccountBundle::CUSTOMER_PORTAL_GROUP_NAME);

        $viewEvent = $this->getViewEvent(new SuccessView());

        $this->notificationHandlerMock->expects(self::atLeastOnce())->method('success');

        $this->viewSubscriber->onView($viewEvent);

        self::assertInstanceOf(RedirectResponse::class, $viewEvent->getResponse());
    }

    public function testRedirectInNonCorporateSiteAccess(): void
    {
        $this->mockSiteAccessService('non_corporate', 'other_group');

        $viewEvent = $this->getViewEvent(new SuccessView());
        $this->viewSubscriber->onView($viewEvent);

        self::assertNull($viewEvent->getResponse());
    }

    public function testRedirectWithNonSuccessViewResult(): void
    {
        $this->mockSiteAccessService('corporate', IbexaCorporateAccountBundle::CUSTOMER_PORTAL_GROUP_NAME);

        $viewEvent = $this->getViewEvent([]);
        $this->viewSubscriber->onView($viewEvent);

        self::assertNull($viewEvent->getResponse());
    }
}
