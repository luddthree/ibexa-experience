<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\Bundle\AdminUi\IbexaAdminUiBundle;
use Ibexa\Bundle\ProductCatalog\EventSubscriber\AbstractLocalViewSubscriber;
use Ibexa\Core\MVC\Symfony\MVCEvents;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Core\MVC\Symfony\SiteAccessGroup;
use Ibexa\ProductCatalog\Config\ConfigProviderInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class AbstractLocalViewSubscriberTest extends TestCase
{
    use SiteAccessServiceMockTrait;

    public function testGetSubscribedEvents(): void
    {
        self::assertEquals(
            [
                MVCEvents::PRE_CONTENT_VIEW => 'onPreContentView',
            ],
            AbstractLocalViewSubscriber::getSubscribedEvents()
        );
    }

    protected function createConfigProvider(bool $isLocal): ConfigProviderInterface
    {
        $configProvider = $this->createMock(ConfigProviderInterface::class);
        $configProvider->method('getEngineType')->willReturn($isLocal ? 'local' : 'remote');

        return $configProvider;
    }

    protected function createFormFactoryMock(): FormFactoryInterface
    {
        $form = $this->createMock(FormInterface::class);
        $form->method('createView')->willReturn($this->createMock(FormView::class));

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->method('create')->willReturn($form);

        return $formFactory;
    }

    protected function createRequestStackMock(): RequestStack
    {
        $requestStack = $this->createMock(RequestStack::class);
        $requestStack
            ->method('getMainRequest')
            ->willReturn(new Request());

        return $requestStack;
    }

    protected function createSiteAccessServiceMock(bool $isAdminSiteAccess): SiteAccessServiceInterface
    {
        $siteAccess = new SiteAccess('siteaccess');
        if ($isAdminSiteAccess) {
            $siteAccess->groups = [
                new SiteAccessGroup(IbexaAdminUiBundle::ADMIN_GROUP_NAME),
            ];
        }

        $siteAccessService = $this->createMock(SiteAccessServiceInterface::class);
        $siteAccessService->method('getCurrent')->willReturn($siteAccess);
        $siteAccessService->method('get')->willReturn($siteAccess);

        return $siteAccessService;
    }
}
