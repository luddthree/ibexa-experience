<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\EventSubscriber;

use Ibexa\Contracts\Core\Repository\Events\Content\BeforeCreateContentEvent;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\User\UserReference;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\CorporateAccount\Exception\ApplicationRateLimitExceededException;
use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\RateLimiter\RateLimiterFactory;

/**
 * @internal
 */
final class ApplicationRateLimitSubscriber implements EventSubscriberInterface
{
    private const RATE_LIMITER_KEY_PATTERN = 'user_%d_ip_%s';

    private RequestStack $requestStack;

    private PermissionResolver $permissionResolver;

    private CorporateAccountConfiguration $configuration;

    private ConfigResolverInterface $configResolver;

    private RateLimiterFactory $corporateAccountApplicationLimiter;

    public function __construct(
        RequestStack $requestStack,
        PermissionResolver $permissionResolver,
        ConfigResolverInterface $configResolver,
        CorporateAccountConfiguration $configuration,
        RateLimiterFactory $corporateAccountApplicationLimiter
    ) {
        $this->requestStack = $requestStack;
        $this->permissionResolver = $permissionResolver;
        $this->configResolver = $configResolver;
        $this->configuration = $configuration;
        $this->corporateAccountApplicationLimiter = $corporateAccountApplicationLimiter;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeCreateContentEvent::class => ['onBeforeCreateContent', 100],
        ];
    }

    public function onBeforeCreateContent(BeforeCreateContentEvent $event): void
    {
        $contentCreateStruct = $event->getContentCreateStruct();
        $currentUser = $this->permissionResolver->getCurrentUserReference();

        if ($this->isApplicationCreateStruct($contentCreateStruct) && $this->isAnonymousUser($currentUser)) {
            $mainRequest = $this->requestStack->getMainRequest();

            if ($mainRequest !== null) {
                $limiter = $this->corporateAccountApplicationLimiter->create(
                    sprintf(
                        self::RATE_LIMITER_KEY_PATTERN,
                        $currentUser->getUserId(),
                        $mainRequest->getClientIp()
                    )
                );

                if (!$limiter->consume()->isAccepted()) {
                    throw new ApplicationRateLimitExceededException($limiter);
                }
            }
        }
    }

    private function isApplicationCreateStruct(ContentCreateStruct $contentCreateStruct): bool
    {
        return $contentCreateStruct->contentType->identifier
            === $this->configuration->getApplicationContentTypeIdentifier();
    }

    private function isAnonymousUser(UserReference $userReference): bool
    {
        return $userReference->getUserId()
            === $this->configResolver->getParameter('anonymous_user_id');
    }
}
