<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Personalization\Controller;

use GuzzleHttp\Exception\BadResponseException;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Migration\Log\LoggerAwareTrait;
use Ibexa\Personalization\Form\Data\CreateAccountData;
use Ibexa\Personalization\Form\Type\CreateAccountType;
use Ibexa\Personalization\Security\Service\SecurityServiceInterface;
use Ibexa\Personalization\Service\Account\AccountServiceInterface;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AccountController extends AbstractSetupController implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private AccountServiceInterface $accountService;

    private TranslatorInterface $translator;

    private TranslatableNotificationHandlerInterface $notificationHandler;

    public function __construct(
        AccountServiceInterface $accountService,
        TranslatorInterface $translator,
        TranslatableNotificationHandlerInterface $notificationHandler,
        SecurityServiceInterface $securityService,
        SettingServiceInterface $settingService,
        ?LoggerInterface $logger = null
    ) {
        parent::__construct($securityService, $settingService);

        $this->logger = $logger ?? new NullLogger();
        $this->accountService = $accountService;
        $this->translator = $translator;
        $this->notificationHandler = $notificationHandler;
        $this->securityService = $securityService;
    }

    public function createAction(Request $request): Response
    {
        $installationKey = $this->settingService->getInstallationKey();

        if (!$this->getAcceptanceStatus($installationKey)->isAccepted()) {
            return $this->redirectToRoute('ibexa.personalization.welcome');
        }

        if (($response = $this->performRedirectToDashboardCheck()) instanceof RedirectResponse) {
            return $response;
        }

        if ($this->settingService->isAccountCreated()) {
            return $this->redirectToRoute('ibexa.personalization.account.details');
        }

        $form = $this->createForm(CreateAccountType::class, new CreateAccountData());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \Ibexa\Personalization\Form\Data\CreateAccountData $data */
            $data = $form->getData();

            try {
                $this->accountService->createAccount($data->getName(), $data->getType());

                return $this->redirectToRoute('ibexa.personalization.account.details');
            } catch (BadResponseException $exception) {
                if (Response::HTTP_SERVICE_UNAVAILABLE === $exception->getCode()) {
                    $this->notificationHandler->warning(
                        /** @Desc("Recommendation engine is not available. Please try again later.") */
                        'recommendation_engine_is_not_available',
                        [],
                        'ibexa_personalization'
                    );
                }

                $this->notificationHandler->warning(
                    /** @Desc("Failed to create new account. Please try again later") */
                    'cannot_create_account',
                    [],
                    'ibexa_personalization'
                );
            }
        }

        return $this->render('@ibexadesign/personalization/account/create.html.twig', [
            'form' => $form->createView(),
            'steps' => $this->getSteps(),
        ]);
    }

    public function detailsAction(Request $request): Response
    {
        $installationKey = $this->settingService->getInstallationKey();

        if (!$this->getAcceptanceStatus($installationKey)->isAccepted()) {
            return $this->redirectToRoute('ibexa.personalization.welcome');
        }

        if (($response = $this->performRedirectToDashboardCheck()) instanceof RedirectResponse) {
            return $response;
        }

        $account = $this->accountService->getAccount();
        if (null === $account) {
            return $this->redirectToRoute('ibexa.personalization.account.create');
        }

        return $this->render('@ibexadesign/personalization/account/details.html.twig', [
            'account' => $account,
            'steps' => $this->getSteps(),
        ]);
    }

    /**
     * @return array<array{
     *     'id': int,
     *     'name': string,
     *     'title': string,
     * }>
     */
    private function getSteps(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'create',
                'title' => $this->translator->trans(
                    /** @Desc("Step %no%") */
                    'create_account.step',
                    ['%no%' => 1],
                    'ibexa_personalization'
                ),
            ],
            [
                'id' => 2,
                'name' => 'details',
                'title' => $this->translator->trans(
                    /** @Desc("Step %no%") */
                    'create_account.step',
                    ['%no%' => 2],
                    'ibexa_personalization'
                ),
            ],
        ];
    }
}
