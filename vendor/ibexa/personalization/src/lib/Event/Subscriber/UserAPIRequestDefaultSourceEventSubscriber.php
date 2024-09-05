<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Event\Subscriber;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Personalization\Event\UpdateUserAPIEvent;
use Ibexa\Personalization\Request\UserMetadataRequest;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class UserAPIRequestDefaultSourceEventSubscriber implements EventSubscriberInterface
{
    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface */
    private $configResolver;

    public function __construct(ConfigResolverInterface $configResolver)
    {
        $this->configResolver = $configResolver;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            UpdateUserAPIEvent::class => ['onRecommendationUpdateUser', 255],
        ];
    }

    public function onRecommendationUpdateUser(UpdateUserAPIEvent $userAPIEvent): void
    {
        if ($userAPIEvent->getUserAPIRequest()) {
            return;
        }

        $userAPIEvent->setUserAPIRequest(new UserMetadataRequest([
            'source' => $this->configResolver->getParameter('personalization.user_api.default_source'),
        ]));
    }
}

class_alias(UserAPIRequestDefaultSourceEventSubscriber::class, 'EzSystems\EzRecommendationClient\Event\Subscriber\UserAPIRequestDefaultSourceEventSubscriber');
