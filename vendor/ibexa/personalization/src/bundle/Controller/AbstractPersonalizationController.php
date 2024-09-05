<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Personalization\Controller;

use GuzzleHttp\Exception\ClientException;
use Ibexa\Bundle\Core\Controller;
use Ibexa\Core\Base\Exceptions\UnauthorizedException;
use Ibexa\Personalization\Exception\InvalidInstallationKeyException;
use Ibexa\Personalization\Permission\CustomerTypeCheckerInterface;
use Ibexa\Personalization\Permission\PermissionCheckerInterface;
use Ibexa\Personalization\Security\PersonalizationPolicyProvider;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use Ibexa\Personalization\Value\Support\AcceptanceStatus;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @internal
 */
abstract class AbstractPersonalizationController extends Controller
{
    protected PermissionCheckerInterface $permissionChecker;

    protected CustomerTypeCheckerInterface $customerTypeChecker;

    protected EventDispatcherInterface $eventDispatcher;

    protected SettingServiceInterface $settingService;

    public function __construct(
        PermissionCheckerInterface $permissionChecker,
        CustomerTypeCheckerInterface $customerTypeChecker,
        EventDispatcherInterface $eventDispatcher,
        SettingServiceInterface $settingService
    ) {
        $this->permissionChecker = $permissionChecker;
        $this->customerTypeChecker = $customerTypeChecker;
        $this->eventDispatcher = $eventDispatcher;
        $this->settingService = $settingService;
    }

    /**
     * @param array<mixed> $parameters
     */
    protected function renderTemplate(int $customerId, string $template, array $parameters = [], Response $response = null): Response
    {
        return $this->render($template, array_merge(
            [
                'can_edit' => $this->permissionChecker->canEdit($customerId),
                'customer_id' => $customerId,
            ],
            $parameters
        ), $response);
    }

    /**
     * @throws \Ibexa\Personalization\Exception\InvalidInstallationKeyException
     */
    protected function performCredentialsCheck(): ?Response
    {
        $installationKey = $this->settingService->getInstallationKey();

        if (
            empty($installationKey)
            || !$this->settingService->getAcceptanceStatus($installationKey)->isAccepted()
            || !$this->settingService->hasCredentials()
        ) {
            return $this->redirectToRoute('ibexa.personalization.welcome');
        }

        $this->checkAcceptanceStatus();

        return null;
    }

    /**
     * @throws \Ibexa\Core\Base\Exceptions\UnauthorizedException
     */
    protected function performAccessCheck(int $customerId): void
    {
        if (!$this->permissionChecker->canView($customerId)) {
            throw new UnauthorizedException(
                PersonalizationPolicyProvider::PERSONALIZATION_MODULE,
                PersonalizationPolicyProvider::PERSONALIZATION_VIEW_FUNCTION,
            );
        }
    }

    /**
     * @throws \Ibexa\Personalization\Exception\InvalidInstallationKeyException
     */
    protected function checkAcceptanceStatus(): AcceptanceStatus
    {
        try {
            $installationKey = $this->settingService->getInstallationKey();

            if (empty($installationKey)) {
                throw new InvalidInstallationKeyException('Missing installation key');
            }

            return $this->settingService->getAcceptanceStatus((string)$installationKey);
        } catch (ClientException $exception) {
            if ($exception->getCode() === Response::HTTP_NOT_FOUND) {
                throw new InvalidInstallationKeyException('Installation key is missing or invalid');
            }

            throw $exception;
        }
    }
}

class_alias(AbstractPersonalizationController::class, 'Ibexa\Platform\Bundle\Personalization\Controller\AbstractPersonalizationController');
