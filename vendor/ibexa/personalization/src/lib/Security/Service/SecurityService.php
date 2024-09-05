<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Security\Service;

use GuzzleHttp\Exception\ClientException;
use Ibexa\Core\Base\Exceptions\UnauthorizedException as CoreUnauthorizedException;
use Ibexa\Personalization\Exception\UnauthorizedException;
use Ibexa\Personalization\Permission\PermissionCheckerInterface;
use Ibexa\Personalization\Security\Limitation\Loader\PersonalizationLimitationListLoaderInterface;
use Ibexa\Personalization\Security\PersonalizationPolicyProvider;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use Ibexa\Personalization\Value\Support\AcceptanceStatus;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 */
final class SecurityService implements SecurityServiceInterface
{
    private PermissionCheckerInterface $permissionChecker;

    private SettingServiceInterface $settingService;

    private PersonalizationLimitationListLoaderInterface $limitationListLoader;

    public function __construct(
        PersonalizationLimitationListLoaderInterface $limitationListLoader,
        PermissionCheckerInterface $permissionChecker,
        SettingServiceInterface $settingService
    ) {
        $this->permissionChecker = $permissionChecker;
        $this->limitationListLoader = $limitationListLoader;
        $this->settingService = $settingService;
    }

    public function getCurrentCustomerId(): ?int
    {
        if (!$this->hasGrantedAccess()) {
            return null;
        }

        $customerAccessList = array_keys($this->getGrantedAccessList());

        return (int)(current($customerAccessList));
    }

    public function hasGrantedAccess(): bool
    {
        return !empty($this->getGrantedAccessList());
    }

    public function getGrantedAccessList(): array
    {
        $grantedAccessList = [];

        foreach ($this->limitationListLoader->getList() as $customerId => $siteName) {
            if ($this->permissionChecker->canView($customerId)) {
                $grantedAccessList[$customerId] = $siteName;
            }
        }

        return $grantedAccessList;
    }

    public function checkAccess(int $customerId): void
    {
        $this->checkAcceptanceStatus();

        if (!$this->permissionChecker->canView($customerId)) {
            throw new CoreUnauthorizedException(
                PersonalizationPolicyProvider::PERSONALIZATION_MODULE,
                PersonalizationPolicyProvider::PERSONALIZATION_VIEW_FUNCTION,
            );
        }
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function checkAcceptanceStatus(): void
    {
        try {
            if (!$this->getAcceptanceStatus()->isAccepted()) {
                throw new UnauthorizedException('Installation key is invalid or has expired', 4);
            }
        } catch (ClientException $exception) {
            if ($exception->getCode() === Response::HTTP_NOT_FOUND) {
                throw new UnauthorizedException('Installation key is missing or invalid', 2, $exception);
            }

            throw new UnauthorizedException(
                'An error occurred when trying to validate installation key',
                3,
                $exception
            );
        }
    }

    /**
     * @throws \Ibexa\Personalization\Exception\UnauthorizedException
     */
    private function getAcceptanceStatus(): AcceptanceStatus
    {
        $installationKey = $this->settingService->getInstallationKey();
        if (empty($installationKey)) {
            throw new UnauthorizedException('Missing installation key', 1);
        }

        return $this->settingService->getAcceptanceStatus($installationKey);
    }
}

class_alias(SecurityService::class, 'Ibexa\Platform\Personalization\Security\Service\SecurityService');
