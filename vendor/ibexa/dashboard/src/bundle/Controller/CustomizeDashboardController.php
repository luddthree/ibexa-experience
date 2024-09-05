<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Dashboard\Controller;

use Exception;
use Ibexa\Bundle\Dashboard\Form\Data\DashboardCustomizeData;
use Ibexa\Bundle\Dashboard\Form\Type\DashboardCustomizeType;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\Dashboard\DashboardServiceInterface;
use Ibexa\Core\MVC\Symfony\Security\Authorization\Attribute;
use JMS\TranslationBundle\Annotation\Ignore;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CustomizeDashboardController extends Controller
{
    private DashboardServiceInterface $dashboardService;

    private FormFactoryInterface $formFactory;

    private TranslatableNotificationHandlerInterface $notificationHandler;

    public function __construct(
        DashboardServiceInterface $dashboardService,
        FormFactoryInterface $formFactory,
        TranslatableNotificationHandlerInterface $notificationHandler
    ) {
        $this->dashboardService = $dashboardService;
        $this->formFactory = $formFactory;
        $this->notificationHandler = $notificationHandler;
    }

    public function customizeDashboardAction(Request $request): Response
    {
        $this->denyAccessUnlessGranted(new Attribute('dashboard', 'customize'));

        $form = $this->formFactory->createNamed(
            'customize-dashboard',
            DashboardCustomizeType::class,
            new DashboardCustomizeData()
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $contentDraft = $this->dashboardService->createCustomDashboardDraft($form->getData()->getLocation());

                return $this->redirectToRoute('ibexa.content.draft.edit', [
                    'contentId' => $contentDraft->id,
                    'versionNo' => $contentDraft->getVersionInfo()->versionNo,
                    'language' => $contentDraft->getDefaultLanguageCode(),
                    'locationId' => $contentDraft->getVersionInfo()->getContentInfo()->getMainLocationId(),
                ]);
            } catch (Exception $e) {
                $this->notificationHandler->error(/** @Ignore */
                    $e->getMessage()
                );
            }
        }

        return $this->redirect($this->generateUrl('ibexa.dashboard'));
    }
}
