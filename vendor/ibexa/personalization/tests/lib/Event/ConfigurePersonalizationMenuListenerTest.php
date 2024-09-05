<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Event;

use Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent;
use Ibexa\AdminUi\Menu\MenuItemFactory;
use Ibexa\Personalization\Event\Listener\ConfigurePersonalizationMenuListener;
use Ibexa\Personalization\Exception\CustomerIdNotFoundException;
use Ibexa\Personalization\Permission\PermissionCheckerInterface;
use Ibexa\Personalization\Security\Service\SecurityServiceInterface;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\MenuItem;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Personalization\Event\Listener\ConfigurePersonalizationMenuListener
 */
final class ConfigurePersonalizationMenuListenerTest extends TestCase
{
    private const CUSTOMER_ID = 12345;
    private const CUSTOMER_ID_WITH_NOT_GRANTED_ACCESS = 0;

    private ConfigurePersonalizationMenuListener $configurePersonalizationMenuListener;

    /** @var \Ibexa\AdminUi\Menu\MenuItemFactory|\PHPUnit\Framework\MockObject\MockObject */
    private MenuItemFactory $menuItemFactory;

    /** @var \Ibexa\Personalization\Security\Service\SecurityServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private SecurityServiceInterface $securityService;

    /** @var \Ibexa\Personalization\Permission\PermissionCheckerInterface|\PHPUnit\Framework\MockObject\MockObject */
    private PermissionCheckerInterface $permissionChecker;

    /** @var \Ibexa\Personalization\Service\Setting\SettingServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private SettingServiceInterface $settingService;

    /** @var \Knp\Menu\FactoryInterface|\PHPUnit\Framework\MockObject\MockObject */
    private FactoryInterface $itemFactory;

    protected function setUp(): void
    {
        $this->menuItemFactory = $this->createMock(MenuItemFactory::class);
        $this->securityService = $this->createMock(SecurityServiceInterface::class);
        $this->permissionChecker = $this->createMock(PermissionCheckerInterface::class);
        $this->settingService = $this->createMock(SettingServiceInterface::class);
        $this->itemFactory = $this->createMock(FactoryInterface::class);
        $this->configurePersonalizationMenuListener = new ConfigurePersonalizationMenuListener(
            $this->menuItemFactory,
            $this->securityService,
            $this->permissionChecker,
            $this->settingService
        );
    }

    public function testRenderMenuWhenCustomerIdIsTakenFromRequest(): void
    {
        $this->settingService
            ->expects(self::atLeastOnce())
            ->method('getCustomerIdFromRequest')
            ->willReturn(self::CUSTOMER_ID);

        $this->permissionChecker
            ->expects(self::once())
            ->method('canView')
            ->with(self::CUSTOMER_ID)
            ->willReturn(true);

        $this->assertRenderMenu(true);
    }

    public function testRenderMenuForFirstCustomerIdWithGrantedAccess(): void
    {
        $this->settingService
            ->expects(self::atLeastOnce())
            ->method('getCustomerIdFromRequest')
            ->willReturn(null);

        $this->securityService
            ->expects(self::atLeastOnce())
            ->method('hasGrantedAccess')
            ->willReturn(true);

        $this->securityService
            ->expects(self::atLeastOnce())
            ->method('getCurrentCustomerId')
            ->willReturn(self::CUSTOMER_ID);

        $this->permissionChecker
            ->expects(self::once())
            ->method('canView')
            ->with(self::CUSTOMER_ID)
            ->willReturn(true);

        $this->assertRenderMenu(true);
    }

    /**
     * @dataProvider provideDataForTestConfigurePersonalizationMenu
     */
    public function testConfigurePersonalizationMenu(
        bool $hasGrantedAccess,
        bool $canView,
        ?int $customerIdFromRequest = null
    ): void {
        $this->settingService
            ->expects(self::atLeastOnce())
            ->method('getCustomerIdFromRequest')
            ->willReturn($customerIdFromRequest);

        $this->securityService
            ->expects(self::atLeastOnce())
            ->method('hasGrantedAccess')
            ->willReturn($hasGrantedAccess);

        $this->permissionChecker
            ->expects(self::once())
            ->method('canView')
            ->with($customerIdFromRequest ?? self::CUSTOMER_ID_WITH_NOT_GRANTED_ACCESS)
            ->willReturn($canView);

        $this->assertRenderMenu($canView);
    }

    public function testThrowExceptionWhenCustomerIdCouldNotBeFound(): void
    {
        $this->settingService
            ->expects(self::once())
            ->method('getCustomerIdFromRequest')
            ->willReturn(null);

        $this->securityService
            ->expects(self::once())
            ->method('hasGrantedAccess')
            ->willReturn(true);

        $this->securityService
            ->expects(self::once())
            ->method('getCurrentCustomerId')
            ->willReturn(null);

        $this->expectException(CustomerIdNotFoundException::class);
        $this->expectExceptionMessage('Customer id not found in current request');

        $this->configurePersonalizationMenuListener->renderMenu($this->createEvent());
    }

    /**
     * @return iterable<array{bool, bool}>
     */
    public function provideDataForTestConfigurePersonalizationMenu(): iterable
    {
        yield 'Render menu when customer id not passed to request but user has granted access' => [
            false,
            true,
        ];

        yield 'Do not render menu when customer id not passed in request and user has not granted access' => [
            false,
            false,
        ];
    }

    private function assertRenderMenu(bool $hasAccess): void
    {
        $event = $this->createEvent();

        if ($hasAccess) {
            $this->configureMenuItemFactoryToReturnMenuItems();
        }

        $this->configurePersonalizationMenuListener->renderMenu($event);

        self::assertEquals($this->createMenu($hasAccess), $event->getMenu());
    }

    private function createMenu(bool $hasAccess): ItemInterface
    {
        $menu = new MenuItem('main', $this->itemFactory);

        if (!$hasAccess) {
            return $menu;
        }

        $personalizationMenu = new MenuItem('main__personalization', $this->itemFactory);
        $personalizationMenu->setChildren($this->getPersonalizationMenuItems());

        $menu->addChild($personalizationMenu);

        return $menu;
    }

    /**
     * @return array<string, \Knp\Menu\MenuItem>
     */
    private function getPersonalizationMenuItems(): array
    {
        $groupSettings = new MenuItem('main__personalization__group_settings', $this->itemFactory);

        $models = new MenuItem('main__personalization__models', $this->itemFactory);
        $models->setParent($groupSettings);

        $scenarios = new MenuItem('main__personalization__scenarios', $this->itemFactory);
        $scenarios->setParent($groupSettings);

        $groupSettings->setChildren([
            'main__personalization__models' => $models,
            'main__personalization__scenarios' => $scenarios,
        ]);

        return [
            'main__personalization__dashboard' => new MenuItem('main__personalization__dashboard', $this->itemFactory),
            'main__personalization__import' => new MenuItem('main__personalization__import', $this->itemFactory),
            'main__personalization__group_settings' => $groupSettings,
        ];
    }

    private function createEvent(): ConfigureMenuEvent
    {
        return new ConfigureMenuEvent(
            $this->itemFactory,
            new MenuItem('main', $this->itemFactory),
            []
        );
    }

    private function configureMenuItemFactoryToReturnMenuItems(): void
    {
        $this->itemFactory
            ->expects(self::atLeastOnce())
            ->method('createItem')
            ->withConsecutive(
                ['main__personalization__models'],
                ['main__personalization__scenarios'],
            )
            ->willReturnOnConsecutiveCalls(
                new MenuItem('main__personalization__models', $this->itemFactory),
                new MenuItem('main__personalization__scenarios', $this->itemFactory),
            );

        $this->menuItemFactory
            ->expects(self::atLeastOnce())
            ->method('createItem')
            ->withConsecutive(
                ['main__personalization'],
                ['main__personalization__group_settings'],
                ['main__personalization__dashboard'],
                ['main__personalization__import'],
            )
            ->willReturnOnConsecutiveCalls(
                new MenuItem('main__personalization', $this->itemFactory),
                new MenuItem('main__personalization__group_settings', $this->itemFactory),
                new MenuItem('main__personalization__dashboard', $this->itemFactory),
                new MenuItem('main__personalization__import', $this->itemFactory),
            );
    }
}
