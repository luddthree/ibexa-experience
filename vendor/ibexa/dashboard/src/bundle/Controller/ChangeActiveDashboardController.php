<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Dashboard\Controller;

use Ibexa\Bundle\Dashboard\Form\Type\DashboardChangeActiveType;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Dashboard\UserSetting\ActiveDashboard;
use Ibexa\User\UserSetting\UserSettingService;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ChangeActiveDashboardController extends Controller
{
    private UserSettingService $userSettingService;

    private TranslatableNotificationHandlerInterface $notificationHandler;

    public function __construct(
        UserSettingService $userSettingService,
        TranslatableNotificationHandlerInterface $notificationHandler
    ) {
        $this->userSettingService = $userSettingService;
        $this->notificationHandler = $notificationHandler;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function changeActiveAction(Request $request, Location $location): Response
    {
        $form = $this->createForm(
            DashboardChangeActiveType::class,
            null,
            [
                'action' => $this->generateUrl(
                    'ibexa.dashboard.change_active',
                    [
                        'locationId' => $location->id,
                    ]
                ),
                'method' => Request::METHOD_POST,
                'current_dashboard' => $location,
            ]
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location */
            $location = $data->getLocation();
            $this->userSettingService->setUserSetting(
                ActiveDashboard::IDENTIFIER,
                $location->remoteId
            );

            $this->notificationHandler->success(
                /** @Desc("Active dashboard is set to '%name%' ") */
                'dashboard.change_active.success',
                [
                    '%name%' => $location->getContent()->getName() ?? '',
                ],
                'ibexa_dashboard'
            );
        }

        return $this->redirect($this->generateUrl('ibexa.dashboard'));
    }
}
