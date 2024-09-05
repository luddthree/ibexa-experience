<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Dashboard\EventSubscriber\Block\QuickActions;

use Ibexa\AdminUi\UniversalDiscovery\Event\ConfigResolveEvent;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class CreateFormQuickActionEventSubscriber implements EventSubscriberInterface
{
    private ConfigResolverInterface $configResolver;

    public function __construct(ConfigResolverInterface $configResolver)
    {
        $this->configResolver = $configResolver;
    }

    public static function getSubscribedEvents(): array
    {
        return [ConfigResolveEvent::NAME => [['onUDWConfigResolve', -100]]];
    }

    public function onUDWConfigResolve(ConfigResolveEvent $event): void
    {
        if ($event->getConfigName() !== 'quick_action_create_form') {
            return;
        }

        $config = $event->getConfig();
        $config['starting_location_id'] = $this->configResolver->getParameter(
            'form_builder.forms_location_id'
        );

        $event->setConfig($config);
    }
}
