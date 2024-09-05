<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Personalization\Controller;

use GuzzleHttp\Exception\BadResponseException;
use Ibexa\Personalization\Form\Data\AcceptanceData;
use Ibexa\Personalization\Form\Type\AcceptanceType;
use Ibexa\Personalization\Security\Service\SecurityServiceInterface;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

final class WelcomeController extends AbstractSetupController
{
    private TranslatorInterface $translator;

    public function __construct(
        SettingServiceInterface $settingService,
        TranslatorInterface $translator,
        SecurityServiceInterface $securityService
    ) {
        parent::__construct($securityService, $settingService);

        $this->translator = $translator;
        $this->securityService = $securityService;
    }

    public function welcomeAction(Request $request): Response
    {
        $installationKey = $this->settingService->getInstallationKey();
        $acceptanceStatus = $this->getAcceptanceStatus($installationKey);
        $accepted = $acceptanceStatus->isAccepted();

        if (
            $accepted
            && ($response = $this->performRedirectToDashboardCheck()) instanceof RedirectResponse
        ) {
            return $response;
        }

        if ($accepted && !$this->settingService->isAccountCreated()) {
            return $this->redirectToRoute('ibexa.personalization.account.create');
        }

        if ($accepted) {
            return $this->redirectToRoute('ibexa.personalization.account.details');
        }

        $acceptanceData = new AcceptanceData();
        if (!empty($installationKey)) {
            $acceptanceData->setInstallationKey($installationKey);
        }
        $form = $this->createForm(AcceptanceType::class, $acceptanceData);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \Ibexa\Personalization\Form\Data\AcceptanceData $acceptanceData */
            $acceptanceData = $form->getData();
            $installationKey = $acceptanceData->getInstallationKey();

            try {
                $this->settingService->acceptTermsAndConditions(
                    $installationKey,
                    $acceptanceData->getUsername(),
                    $acceptanceData->getEmail()
                );

                $acceptanceStatus = $this->settingService->getAcceptanceStatus($installationKey);

                if ($acceptanceStatus->isAccepted()) {
                    $this->settingService->setInstallationKey($installationKey);

                    return $this->redirectToRoute('ibexa.personalization.account.create');
                }
            } catch (BadResponseException $exception) {
                $form->addError(
                    new FormError(
                        $this->translator->trans(
                            /** @Desc("This installation key is not valid.") */
                            'welcome.form.invalid_installation_key',
                            [],
                            'ibexa_personalization'
                        )
                    )
                );
            }
        }

        return $this->render('@ibexadesign/personalization/welcome/acceptance.html.twig', [
            'acceptanceStatus' => $acceptanceStatus,
            'form' => $form->createView(),
            'termsAndConditions' => $this->settingService->getTermsAndConditions(),
        ]);
    }
}

class_alias(WelcomeController::class, 'Ibexa\Platform\Bundle\Personalization\Controller\WelcomeController');
