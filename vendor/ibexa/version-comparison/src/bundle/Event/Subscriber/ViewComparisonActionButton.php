<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\VersionComparison\Event\Subscriber;

use Ibexa\AdminUi\Tab\Event\TabEvents;
use Ibexa\AdminUi\Tab\Event\TabViewRenderEvent;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ViewComparisonActionButton implements EventSubscriberInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    public function __construct(PermissionResolver $permissionResolver)
    {
        $this->permissionResolver = $permissionResolver;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TabEvents::TAB_RENDER => ['onTabRender', -10],
        ];
    }

    public function onTabRender(TabViewRenderEvent $event): void
    {
        if ($event->getTabIdentifier() !== 'versions') {
            return;
        }

        if (!$this->permissionResolver->hasAccess('comparison', 'view')) {
            return;
        }

        $parameters = $event->getParameters();

        // Inject to original tab
        $parameters['table_template_path'] = '@IbexaVersionComparison/themes/admin/version_comparison/admin/content_view/tab/versions/table.html.twig';
        $event->setParameters($parameters);
    }
}

class_alias(ViewComparisonActionButton::class, 'EzSystems\EzPlatformVersionComparisonBundle\Event\Subscriber\ViewComparisonActionButton');
