<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Menu;

use Ibexa\AdminUi\Menu\MenuItemFactory;
use Ibexa\Contracts\AdminUi\Menu\AbstractBuilder;
use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\CorporateAccount\CustomerPortal\CustomerPortalResolver;
use Ibexa\Contracts\CorporateAccount\Permission\MemberResolver;
use Ibexa\Core\MVC\Symfony\Routing\UrlAliasRouter;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class DynamicCustomerPortalMenuBuilder extends AbstractBuilder implements TranslationContainerInterface
{
    public const EVENT_NAME = 'ibexa_customer_portal.menu_configure.dynamic_main_menu';

    public const ITEM_DASHBOARD = 'customer_portal__dashboard';

    public const ITEM_ORDER_MANAGEMENT = 'customer_portal__order_management';
    public const ITEM_ORDER_MANAGEMENT__PENDING_ORDERS = 'customer_portal__order_management__pending_orders';
    public const ITEM_ORDER_MANAGEMENT__PAST_ORDERS = 'customer_portal__order_management__past_orders';

    public const ITEM_ORGANIZATION = 'customer_portal__organization';
    public const ITEM_ORGANIZATION__CONTACT = 'customer_portal__organization__contact';
    public const ITEM_ORGANIZATION__MEMBERS = 'customer_portal__organization__members';
    public const ITEM_ORGANIZATION__ADDRESS_BOOK = 'customer_portal__organization__address_book';

    public const ITEM_ACCOUNT_SETTINGS = 'customer_portal__account_settings';
    public const ITEM_ACCOUNT_SETTINGS__MY_PROFILE = 'customer_portal__account_settings__my_profile';
    private const TRANSLATION_DOMAIN = 'ibexa_menu';

    private SiteAccessServiceInterface $siteAccessService;

    private LocationService $locationService;

    private CustomerPortalResolver $customerPortalResolver;

    private MemberResolver $memberResolver;

    public function __construct(
        MenuItemFactory $factory,
        EventDispatcherInterface $eventDispatcher,
        SiteAccessServiceInterface $siteAccessService,
        LocationService $locationService,
        CustomerPortalResolver $customerPortalResolver,
        MemberResolver $memberResolver
    ) {
        parent::__construct($factory, $eventDispatcher);
        $this->siteAccessService = $siteAccessService;
        $this->locationService = $locationService;
        $this->customerPortalResolver = $customerPortalResolver;
        $this->memberResolver = $memberResolver;
    }

    protected function getConfigureEventName(): string
    {
        return self::EVENT_NAME;
    }

    /**
     * @param array<mixed> $options
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function createStructure(array $options): ItemInterface
    {
        /** @var \Knp\Menu\ItemInterface|\Knp\Menu\ItemInterface[] $menu */
        $menu = $this->createMenuItem('root');

        $currentSiteAccess = $this->siteAccessService->getCurrent();

        if ($currentSiteAccess === null) {
            return $menu;
        }

        $currentMember = null;
        $portalLocation = null;

        try {
            $currentMember = $this->memberResolver->getCurrentMember();
        } catch (InvalidArgumentException $exception) {
            //Not a member, cant resolve portal, fallback to default menu.
        }

        if ($currentMember !== null) {
            $portalLocation = $this->customerPortalResolver->resolveCustomerPortalLocation(
                $currentMember,
                $currentSiteAccess->name
            );
        }

        if ($portalLocation !== null) {
            $subPages = $this->locationService->loadLocationChildren(
                $portalLocation
            );
            $sortedPages = $subPages->locations;
            usort(
                $sortedPages,
                static fn (Location $a, Location $b): int => $b->priority <=> $a->priority
            );

            foreach ($sortedPages as $page) {
                $menu->addChild((string) $page->id, [
                    'label' => $page->getContent()->getName(),
                    'route' => UrlAliasRouter::URL_ALIAS_ROUTE_NAME,
                    'routeParameters' => [
                        'contentId' => $page->getContent()->id,
                        'locationId' => $page->id,
                    ],
                ]);
            }
        } else {
            $menu->addChild(self::ITEM_DASHBOARD, [
                'route' => 'ibexa.corporate_account.customer_portal.customer_center',
                'label' => self::ITEM_DASHBOARD,
            ]);
        }

        $orderManagementMenu = $menu->addChild(self::ITEM_ORDER_MANAGEMENT, [
            'label' => self::ITEM_ORDER_MANAGEMENT,
        ]);
        $orderManagementMenu->addChild(self::ITEM_ORDER_MANAGEMENT__PENDING_ORDERS, [
            'route' => 'ibexa.corporate_account.customer_portal.pending_orders',
            'label' => self::ITEM_ORDER_MANAGEMENT__PENDING_ORDERS,
        ]);
        $orderManagementMenu->addChild(self::ITEM_ORDER_MANAGEMENT__PAST_ORDERS, [
            'route' => 'ibexa.corporate_account.customer_portal.past_orders',
            'label' => self::ITEM_ORDER_MANAGEMENT__PAST_ORDERS,
        ]);

        $organizationMenu = $menu->addChild(self::ITEM_ORGANIZATION, [
            'label' => self::ITEM_ORGANIZATION,
        ]);
        $organizationMenu->addChild(self::ITEM_ORGANIZATION__CONTACT, [
            'route' => 'ibexa.corporate_account.customer_portal.contact',
            'label' => self::ITEM_ORGANIZATION__CONTACT,
        ]);
        $organizationMenu->addChild(self::ITEM_ORGANIZATION__MEMBERS, [
            'route' => 'ibexa.corporate_account.customer_portal.members',
            'label' => self::ITEM_ORGANIZATION__MEMBERS,
        ]);
        $organizationMenu->addChild(self::ITEM_ORGANIZATION__ADDRESS_BOOK, [
            'route' => 'ibexa.corporate_account.customer_portal.address_book',
            'label' => self::ITEM_ORGANIZATION__ADDRESS_BOOK,
        ]);

        $settingsMenu = $menu->addChild(self::ITEM_ACCOUNT_SETTINGS, [
            'label' => self::ITEM_ACCOUNT_SETTINGS,
        ]);
        $settingsMenu->addChild(self::ITEM_ACCOUNT_SETTINGS__MY_PROFILE, [
            'route' => 'ibexa.corporate_account.customer_portal.my_profile',
            'label' => self::ITEM_ACCOUNT_SETTINGS__MY_PROFILE,
        ]);

        return $menu;
    }

    /**
     * @return \JMS\TranslationBundle\Model\Message[]
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM_DASHBOARD, self::TRANSLATION_DOMAIN))->setDesc('Dashboard'),
            (new Message(self::ITEM_ORDER_MANAGEMENT, self::TRANSLATION_DOMAIN))->setDesc('Order management'),
            (new Message(self::ITEM_ORDER_MANAGEMENT__PENDING_ORDERS, self::TRANSLATION_DOMAIN))->setDesc('Pending orders'),
            (new Message(self::ITEM_ORDER_MANAGEMENT__PAST_ORDERS, self::TRANSLATION_DOMAIN))->setDesc('Past orders'),
            (new Message(self::ITEM_ORGANIZATION, self::TRANSLATION_DOMAIN))->setDesc('Organization'),
            (new Message(self::ITEM_ORGANIZATION__CONTACT, self::TRANSLATION_DOMAIN))->setDesc('Contact'),
            (new Message(self::ITEM_ORGANIZATION__MEMBERS, self::TRANSLATION_DOMAIN))->setDesc('Members'),
            (new Message(self::ITEM_ORGANIZATION__ADDRESS_BOOK, self::TRANSLATION_DOMAIN))->setDesc('Address book'),
            (new Message(self::ITEM_ACCOUNT_SETTINGS, self::TRANSLATION_DOMAIN))->setDesc('Settings'),
            (new Message(self::ITEM_ACCOUNT_SETTINGS__MY_PROFILE, self::TRANSLATION_DOMAIN))->setDesc('My profile'),
        ];
    }
}
