<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Personalization\Controller;

use Ibexa\Personalization\Security\Service\SecurityServiceInterface;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use Ibexa\Personalization\Value\Support\AcceptanceStatus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;

abstract class AbstractSetupController extends AbstractController
{
    protected SecurityServiceInterface $securityService;

    protected SettingServiceInterface $settingService;

    public function __construct(
        SecurityServiceInterface $securityService,
        SettingServiceInterface $settingService
    ) {
        $this->securityService = $securityService;
        $this->settingService = $settingService;
    }

    protected function getAcceptanceStatus(?string $installationKey): AcceptanceStatus
    {
        return empty($installationKey)
            ? new AcceptanceStatus(false)
            : $this->settingService->getAcceptanceStatus($installationKey);
    }

    /**
     * Redirect directly to dashboard if there is configured customerId with granted access.
     */
    protected function performRedirectToDashboardCheck(): ?RedirectResponse
    {
        $firstConfiguredCustomerId = $this->securityService->getCurrentCustomerId();
        if (null === $firstConfiguredCustomerId) {
            return null;
        }

        return $this->redirectToRoute('ibexa.personalization.dashboard', [
            'customerId' => $firstConfiguredCustomerId,
        ]);
    }
}
