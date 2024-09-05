<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Dashboard\EventSubscriber;

use Ibexa\Bundle\Dashboard\DependencyInjection\Parser\Dashboard;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup;
use Ibexa\Tests\Dashboard\PhpUnit\ConfigResolverMockTrait;
use Ibexa\Tests\Dashboard\PhpUnit\ContentItemOfContentTypeMockTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;

final class DashboardTypeSubscriberTest extends TestCase
{
    use ConfigResolverMockTrait;
    use ContentItemOfContentTypeMockTrait;

    private DashboardTypeSubscriber $dashboardTypeSubscriber;

    public function setUp(): void
    {
        $groups = [
            $this->getContentTypeGroupMock(66),
        ];
        $configResolver = $this->mockConfigResolver();
        $contentTypeService = $this->createMock(ContentTypeService::class);
        $contentTypeService
            ->method('loadContentTypeByIdentifier')
            ->with(Dashboard::DASHBOARD_CONTENT_TYPE_IDENTIFIER)
            ->willReturn($this->mockDashboardContentType($groups));
        $router = $this->createMock(RouterInterface::class);
        $router
            ->method('generate')
            ->with('ibexa.dashboard.content_type')
            ->willReturn('redirectUrl');

        $this->dashboardTypeSubscriber = new DashboardTypeSubscriber(
            $contentTypeService,
            $configResolver,
            $router
        );
    }

    public function testGetSubscribedEvents(): void
    {
        self::assertEquals(
            [
                KernelEvents::REQUEST => ['onKernelController'],
            ],
            DashboardTypeSubscriber::getSubscribedEvents()
        );
    }

    public function testOnKernelController(): void
    {
        $event = $this->createEvent('ibexa.content_type.view', 66);
        $this->dashboardTypeSubscriber->onKernelController($event);
        $response = $event->getResponse();

        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertSame('redirectUrl', $response->getTargetUrl());
    }

    public function testOnKernelControllerNoContentTypeRedirect(): void
    {
        $event = $this->createEvent('no.content_type.view', 66);
        $this->dashboardTypeSubscriber->onKernelController($event);
        $response = $event->getResponse();

        self::assertNull($response);
    }

    public function testOnKernelControllerNoDashboardTypeGroup(): void
    {
        $event = $this->createEvent('ibexa.content_type.view', 1);
        $this->dashboardTypeSubscriber->onKernelController($event);
        $response = $event->getResponse();

        self::assertNull($response);
    }

    private function createEvent(string $route, int $contentTypeGroupId): RequestEvent
    {
        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = $this->createMock(Request::class);
        $request
            ->method('get')
            ->willReturnMap([
                ['_route', null, $route],
                ['contentTypeGroupId', null, $contentTypeGroupId],
            ]);

        return new RequestEvent(
            $kernel,
            $request,
            1
        );
    }

    public function getContentTypeGroupMock(int $id): ContentTypeGroup
    {
        $contentTypeGroupMock = $this->createMock(ContentTypeGroup::class);
        $contentTypeGroupMock
            ->method('__get')
            ->with('id')
            ->willReturn($id);

        return $contentTypeGroupMock;
    }
}
