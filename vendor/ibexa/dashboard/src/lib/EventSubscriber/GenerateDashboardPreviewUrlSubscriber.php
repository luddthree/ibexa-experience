<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Dashboard\EventSubscriber;

use Ibexa\Bundle\Dashboard\ViewBuilder\DashboardViewBuilder;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\PageBuilder\Event\GenerateContentPreviewUrlEvent;
use Ibexa\Dashboard\Specification\IsDashboardContentType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @internal
 */
final class GenerateDashboardPreviewUrlSubscriber implements EventSubscriberInterface
{
    private ConfigResolverInterface $configResolver;

    public function __construct(ConfigResolverInterface $configResolver)
    {
        $this->configResolver = $configResolver;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            GenerateContentPreviewUrlEvent::NAME => ['onGenerateContentPreviewUrl', 0],
        ];
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function onGenerateContentPreviewUrl(GenerateContentPreviewUrlEvent $event): void
    {
        if (
            (new IsDashboardContentType($this->configResolver))->isSatisfiedBy($event->getContent()->getContentType())
        ) {
            $event->addParameter('viewType', DashboardViewBuilder::DASHBOARD_VIEW_TYPE);
        }
    }
}
