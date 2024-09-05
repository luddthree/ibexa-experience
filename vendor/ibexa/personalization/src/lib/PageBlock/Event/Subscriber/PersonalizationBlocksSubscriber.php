<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\PageBlock\Event\Subscriber;

use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinitionEvents;
use Ibexa\FieldTypePage\FieldType\Page\Block\Definition\Event\BlockDefinitionEvent;
use Ibexa\Personalization\Security\Service\SecurityServiceInterface;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class PersonalizationBlocksSubscriber implements EventSubscriberInterface
{
    private const BLOCK_IDENTIFIER_PERSONALIZED = 'personalized';
    private const BLOCK_IDENTIFIER_DYNAMIC_TARGETING = 'dynamic_targeting';
    private const BLOCK_IDENTIFIER_LAST_VIEWED = 'last_viewed';
    private const BLOCK_IDENTIFIER_LAST_PURCHASED = 'last_purchased';
    private const BLOCK_IDENTIFIER_RECENTLY_ADDED = 'recently_added';
    private const BLOCK_IDENTIFIER_BESTSELLERS = 'bestsellers';

    private SecurityServiceInterface $securityService;

    private SettingServiceInterface $settingService;

    public function __construct(
        SecurityServiceInterface $securityService,
        SettingServiceInterface $settingService
    ) {
        $this->securityService = $securityService;
        $this->settingService = $settingService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BlockDefinitionEvents::getBlockDefinitionEventName(self::BLOCK_IDENTIFIER_PERSONALIZED) => 'onBlockDefinition',
            BlockDefinitionEvents::getBlockDefinitionEventName(self::BLOCK_IDENTIFIER_DYNAMIC_TARGETING) => 'onBlockDefinition',
            BlockDefinitionEvents::getBlockDefinitionEventName(self::BLOCK_IDENTIFIER_LAST_VIEWED) => 'onBlockDefinition',
            BlockDefinitionEvents::getBlockDefinitionEventName(self::BLOCK_IDENTIFIER_LAST_PURCHASED) => 'onBlockDefinition',
            BlockDefinitionEvents::getBlockDefinitionEventName(self::BLOCK_IDENTIFIER_RECENTLY_ADDED) => 'onBlockDefinition',
            BlockDefinitionEvents::getBlockDefinitionEventName(self::BLOCK_IDENTIFIER_BESTSELLERS) => 'onBlockDefinition',
        ];
    }

    public function onBlockDefinition(BlockDefinitionEvent $blockDefinitionEvent): void
    {
        if (empty($this->settingService->getInstallationKey()) || !$this->hasCredentials()) {
            $blockDefinitionEvent->getDefinition()->setVisible(false);
        }
    }

    private function hasCredentials(): bool
    {
        $customerId = $this->securityService->getCurrentCustomerId();

        return !empty($customerId)
            && !empty($this->settingService->getLicenceKeyByCustomerId($customerId));
    }
}
