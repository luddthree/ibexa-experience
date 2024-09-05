<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\SiteFactory\Event\Subscriber;

use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Exceptions\LimitationValidationException;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\RoleService;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\SiteAccessLimitation;
use Ibexa\Contracts\Core\Repository\Values\User\PolicyDraft;
use Ibexa\Contracts\Core\Repository\Values\User\RoleDraft;
use Ibexa\Contracts\SiteFactory\Events\CreateSiteEvent;
use Ibexa\Contracts\SiteFactory\Events\DeleteSiteEvent;
use Ibexa\Contracts\SiteFactory\Events\UpdateSiteEvent;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class UpdateRolesSubscriber implements EventSubscriberInterface
{
    use LoggerAwareTrait;

    /** @var \Ibexa\Contracts\Core\Repository\RoleService */
    private $roleService;

    /** @var string[] */
    private $updateRoles;

    public function __construct(
        RoleService $roleService,
        array $updateRoles
    ) {
        $this->roleService = $roleService;
        $this->updateRoles = $updateRoles;
        $this->logger = new NullLogger();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CreateSiteEvent::class => ['onSiteCreate', 0],
            DeleteSiteEvent::class => ['onSiteDelete', 0],
            UpdateSiteEvent::class => ['onSiteUpdate', 0],
        ];
    }

    public function onSiteCreate(CreateSiteEvent $event): void
    {
        $publicAccessIdentifiers = $this->getSiteAccessIdentifiers($event);

        foreach ($this->updateRoles as $roleIdentifier) {
            try {
                $roleDraft = $this->getRoleDraft($roleIdentifier);

                foreach ($this->getLoginPolicies($roleDraft) as $loginPolicy) {
                    foreach ($loginPolicy->getLimitations() as $limitation) {
                        if ($limitation instanceof SiteAccessLimitation) {
                            $updatedLimitationValues = array_merge($limitation->limitationValues, $publicAccessIdentifiers);
                            $this->updateSiteAccessLimitation($roleDraft, $loginPolicy, $updatedLimitationValues);
                        }
                    }
                }

                $this->roleService->publishRoleDraft($roleDraft);
            } catch (UnauthorizedException | LimitationValidationException | InvalidArgumentException | NotFoundException $e) {
                if (isset($roleDraft)) {
                    $this->roleService->deleteRoleDraft($roleDraft);
                }

                $this->logger->warning(sprintf(
                    'Can not update Role with identifier %s after creation of the Site with ID %d: %s',
                    $roleIdentifier,
                    $event->getSite()->id,
                    $e->getMessage()
                ));
            }
        }
    }

    public function onSiteDelete(DeleteSiteEvent $event): void
    {
        $publicAccessIdentifiers = [];
        foreach ($event->getSite()->publicAccesses as $publicAccess) {
            $publicAccessIdentifiers[] = $this->generateSiteAccessValue($publicAccess->identifier);
        }

        foreach ($this->updateRoles as $roleIdentifier) {
            try {
                $roleDraft = $this->getRoleDraft($roleIdentifier);

                foreach ($this->getLoginPolicies($roleDraft) as $loginPolicy) {
                    foreach ($loginPolicy->getLimitations() as $limitation) {
                        if ($limitation instanceof SiteAccessLimitation) {
                            $updatedLimitationValues = array_diff($limitation->limitationValues, $publicAccessIdentifiers);
                            $this->updateSiteAccessLimitation($roleDraft, $loginPolicy, $updatedLimitationValues);
                        }
                    }
                }

                $this->roleService->publishRoleDraft($roleDraft);
            } catch (UnauthorizedException | LimitationValidationException | InvalidArgumentException | NotFoundException $e) {
                if (isset($roleDraft)) {
                    $this->roleService->deleteRoleDraft($roleDraft);
                }
                $this->logger->warning(sprintf(
                    'Can not update Role with identifier %s after deletion of the Site with ID %d: %s',
                    $roleIdentifier,
                    $event->getSite()->id,
                    $e->getMessage()
                ));
            }
        }
    }

    public function onSiteUpdate(UpdateSiteEvent $event): void
    {
        $publicAccessIdentifiersBefore = [];
        $publicAccessIdentifiersAfter = [];

        foreach ($event->getSite()->publicAccesses as $publicAccess) {
            $publicAccessIdentifiersBefore[] = $this->generateSiteAccessValue($publicAccess->identifier);
        }
        foreach ($event->getUpdatedSite()->publicAccesses as $publicAccess) {
            $publicAccessIdentifiersAfter[] = $this->generateSiteAccessValue($publicAccess->identifier);
        }

        $addedPublicAccessIdentifiers = array_diff($publicAccessIdentifiersAfter, $publicAccessIdentifiersBefore);
        $deletedPublicAccessIdentifiers = array_diff($publicAccessIdentifiersBefore, $publicAccessIdentifiersAfter);

        foreach ($this->updateRoles as $roleIdentifier) {
            try {
                $roleDraft = $this->getRoleDraft($roleIdentifier);

                foreach ($this->getLoginPolicies($roleDraft) as $loginPolicy) {
                    foreach ($loginPolicy->getLimitations() as $limitation) {
                        if ($limitation instanceof SiteAccessLimitation) {
                            $updatedLimitationValues = array_merge($limitation->limitationValues, $addedPublicAccessIdentifiers);
                            $updatedLimitationValues = array_diff($updatedLimitationValues, $deletedPublicAccessIdentifiers);
                            $this->updateSiteAccessLimitation($roleDraft, $loginPolicy, $updatedLimitationValues);
                        }
                    }
                }

                $this->roleService->publishRoleDraft($roleDraft);
            } catch (UnauthorizedException | LimitationValidationException | InvalidArgumentException | NotFoundException $e) {
                if (isset($roleDraft)) {
                    $this->roleService->deleteRoleDraft($roleDraft);
                }
                $this->logger->warning(sprintf(
                    'Can not update Role with identifier %s after edition of the Site with ID %d: %s',
                    $roleIdentifier,
                    $event->getSite()->id,
                    $e->getMessage()
                ));
            }
        }
    }

    /**
     * Generates the SiteAccess value as CRC32.
     */
    private function generateSiteAccessValue(string $sa): string
    {
        return sprintf('%u', crc32($sa));
    }

    /**
     * @return string[]
     */
    private function getSiteAccessIdentifiers(CreateSiteEvent $event): array
    {
        $publicAccessIdentifiers = [];

        foreach ($event->getSiteCreateStruct()->publicAccesses as $publicAccess) {
            $publicAccessIdentifiers[] = $this->generateSiteAccessValue($publicAccess->identifier);
        }

        return $publicAccessIdentifiers;
    }

    /**
     * Get `user/login` Policies from the Role. They are fetched to perform an update on SiteAccess limitation values.
     * If a Role has no `user/login` Policy then information to logs is written.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\PolicyDraft[]
     */
    private function getLoginPolicies(RoleDraft $roleDraft): array
    {
        /** @var \Ibexa\Contracts\Core\Repository\Values\User\PolicyDraft $policy */
        $loginPolicies = [];
        foreach ($roleDraft->getPolicies() as $policy) {
            if ('user' !== $policy->module || 'login' !== $policy->function) {
                continue;
            }
            $loginPolicies[] = $policy;
        }

        if (empty($loginPolicies)) {
            $this->logger->warning(
                sprintf(
                    'Role with ID: %d which is configured to update when new site is created has no user/login policy.
                    Please check your configuration.',
                    $roleDraft->id
                )
            );
        }

        return $loginPolicies;
    }

    /**
     * @param string[] $limitationValues
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\LimitationValidationException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    private function updateSiteAccessLimitation(
        RoleDraft $roleDraft,
        PolicyDraft $policyDraft,
        array $limitationValues
    ): PolicyDraft {
        $policyUpdate = $this->roleService->newPolicyUpdateStruct();
        $policyUpdate->addLimitation(
            new SiteAccessLimitation(
                [
                    'limitationValues' => $limitationValues,
                ]
            )
        );

        return $this->roleService->updatePolicyByRoleDraft(
            $roleDraft,
            $policyDraft,
            $policyUpdate
        );
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\LimitationValidationException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    private function getRoleDraft(string $roleIdentifier): RoleDraft
    {
        $role = $this->roleService->loadRoleByIdentifier($roleIdentifier);

        return $this->roleService->createRoleDraft($role);
    }
}

class_alias(UpdateRolesSubscriber::class, 'EzSystems\EzPlatformSiteFactory\Event\Subscriber\UpdateRolesSubscriber');
