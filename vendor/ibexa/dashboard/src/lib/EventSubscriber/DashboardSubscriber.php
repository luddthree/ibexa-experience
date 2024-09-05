<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Dashboard\EventSubscriber;

use Ibexa\Bundle\Dashboard\Controller\DashboardController;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Dashboard\Exception\MissingDashboardLocationException;
use Ibexa\User\UserSetting\UserSettingService;
use JMS\TranslationBundle\Annotation\Desc;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

final class DashboardSubscriber implements EventSubscriberInterface
{
    use LoggerAwareTrait;

    private const STATIC_DASHBOARD_ROUTE = 'ibexa.dashboard';

    private LocationService $locationService;

    private UserSettingService $userSettingService;

    private Environment $twig;

    private TranslatableNotificationHandlerInterface $notificationHandler;

    private ConfigResolverInterface $configResolver;

    public function __construct(
        LocationService $locationService,
        UserSettingService $userSettingService,
        Environment $twig,
        TranslatableNotificationHandlerInterface $notificationHandler,
        ConfigResolverInterface $configResolver,
        ?LoggerInterface $logger = null
    ) {
        $this->locationService = $locationService;
        $this->userSettingService = $userSettingService;
        $this->twig = $twig;
        $this->notificationHandler = $notificationHandler;
        $this->configResolver = $configResolver;
        $this->logger = $logger ?? new NullLogger();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 0],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (HttpKernelInterface::MAIN_REQUEST !== $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();

        if ($request->get('_route') !== self::STATIC_DASHBOARD_ROUTE) {
            return;
        }

        try {
            $dashboardLocation = $this->getDashboardLocation();
        } catch (MissingDashboardLocationException $e) {
            $this->logger->error($e->getMessage());
            $this->notificationHandler->error(
                /** @Desc("The dashboard could not be loaded") */
                'dashboard.error.unable_to_load',
                [],
                'ibexa_dashboard'
            );

            $response = new Response();
            $response->setContent(
                $this->twig->render('@ibexadesign/dashboard/dashboard_error.html.twig')
            );
            $event->setResponse($response);

            return;
        }

        $request->attributes->set('contentId', $dashboardLocation->getContentInfo()->getId());
        $request->attributes->set('locationId', $dashboardLocation->id);

        $event->getRequest()->attributes->set(
            '_controller',
            DashboardController::class . '::dashboardAction'
        );
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Location
     */
    private function getDashboardLocation(): Location
    {
        try {
            try {
                $activeDashboard = $this->userSettingService->getUserSetting('active_dashboard')->value;
                $location = $this->locationService->loadLocationByRemoteId($activeDashboard);
            } catch (NotFoundException|UnauthorizedException|InvalidArgumentException $e) {
                $defaultDashboardRemoteId = $this->configResolver->getParameter('dashboard.default_dashboard_remote_id');
                $location = $this->locationService->loadLocationByRemoteId($defaultDashboardRemoteId);
            }
        } catch (NotFoundException|UnauthorizedException $e) {
            throw new MissingDashboardLocationException('The dashboard could not be loaded. ' . $e->getMessage());
        }

        return $location;
    }
}
