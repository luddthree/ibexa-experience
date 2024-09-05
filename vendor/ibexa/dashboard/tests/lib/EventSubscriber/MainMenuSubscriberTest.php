<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Dashboard\EventSubscriber;

use Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent;
use Ibexa\Dashboard\EventSubscriber\MainMenuSubscriber;
use Knp\Menu\FactoryInterface;
use Knp\Menu\MenuItem;
use PHPUnit\Framework\TestCase;

final class MainMenuSubscriberTest extends TestCase
{
    /** @var \Knp\Menu\FactoryInterface&\PHPUnit\Framework\MockObject\MockObject */
    private FactoryInterface $itemFactory;

    private MainMenuSubscriber $mainMenuSubscriber;

    protected function setUp(): void
    {
        $this->itemFactory = $this->getItemFactoryMock();

        $this->mainMenuSubscriber = new MainMenuSubscriber();
    }

    public function testGetSubscribedEvents(): void
    {
        self::assertEquals(
            [
                ConfigureMenuEvent::MAIN_MENU => ['onConfigureMainMenu'],
            ],
            MainMenuSubscriber::getSubscribedEvents()
        );
    }

    public function testOnConfigureMainMenu(): void
    {
        $event = $this->createEvent();

        $this->mainMenuSubscriber->onConfigureMainMenu($event);

        $mainMenu = $event->getMenu();
        $contentMenu = $mainMenu->getChild('main__content');
        self::assertNotNull($contentMenu);
        self::assertNotNull($contentMenu->getChild('main__content__content_structure'));
        self::assertNull($contentMenu->getChild('main__content__dashboard'));

        self::assertNotNull($mainMenu->getChild('main__customizable_dashboard'));
    }

    private function createEvent(): ConfigureMenuEvent
    {
        $mainMenu = new MenuItem('main', $this->itemFactory);

        $contentMenu = new MenuItem('main__content', $this->itemFactory);
        $contentMenu->setChildren($this->getContentMenuItems());

        $mainMenu->addChild($contentMenu);

        return new ConfigureMenuEvent(
            $this->itemFactory,
            $mainMenu
        );
    }

    /**
     * @return array<string, \Knp\Menu\MenuItem>
     */
    private function getContentMenuItems(): array
    {
        return [
            'main__dashboard' => new MenuItem('main__dashboard', $this->itemFactory),
            'main__content__content_structure' => new MenuItem('main__content__content_structure', $this->itemFactory),
        ];
    }

    /**
     * @return \Knp\Menu\FactoryInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private function getItemFactoryMock(): FactoryInterface
    {
        $itemFactory = $this->createMock(FactoryInterface::class);
        $itemFactory->method('createItem')
            ->willReturnCallback(static fn (string $name) => new MenuItem($name, $itemFactory))
        ;

        return $itemFactory;
    }
}
