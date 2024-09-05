<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Dashboard\EventSubscriber;

use Ibexa\AdminUi\Form\Type\Event\ContentCreateContentTypeChoiceLoaderEvent;
use Ibexa\Contracts\Core\Persistence\Content\Location\Handler;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Dashboard\Specification\IsDashboardContentType;
use Ibexa\Dashboard\Specification\IsInDashboardTreeRoot;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ContentCreateContentTypeChoiceLoaderSubscriber implements EventSubscriberInterface
{
    private ConfigResolverInterface $configResolver;

    private ContentTypeService $contentTypeService;

    private Handler $locationHandler;

    public function __construct(
        ConfigResolverInterface $configResolver,
        ContentTypeService $contentTypeService,
        Handler $locationHandler
    ) {
        $this->configResolver = $configResolver;
        $this->contentTypeService = $contentTypeService;
        $this->locationHandler = $locationHandler;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ContentCreateContentTypeChoiceLoaderEvent::RESOLVE_CONTENT_TYPES => 'removeDashboardContentType',
        ];
    }

    public function removeDashboardContentType(ContentCreateContentTypeChoiceLoaderEvent $event): void
    {
        $location = $event->getTargetLocation();
        if ($location === null) {
            return;
        }

        try {
            $dashboardContentType = $this->contentTypeService->loadContentTypeByIdentifier(
                $this->configResolver->getParameter(
                    IsDashboardContentType::CONTENT_TYPE_IDENTIFIER_PARAM_NAME
                )
            );
        } catch (NotFoundException $e) {
            return;
        }

        $dashboardGroups = $dashboardContentType->getContentTypeGroups();
        if ($this->isInDashboardTreeRoot($location)) {
            $groups = [];
            $dashboardContainer = $this->configResolver->getParameter(
                'dashboard.container_content_type_identifier'
            );
            $folderContentType = $this->contentTypeService->loadContentTypeByIdentifier(
                $dashboardContainer
            );
            foreach ($dashboardGroups as $dashboardGroup) {
                $groups[$dashboardGroup->identifier] = [
                    $dashboardContentType,
                    $folderContentType,
                ];
            }
            $event->setContentTypeGroups($groups);
        } else {
            foreach ($dashboardGroups as $dashboardGroup) {
                $event->removeContentTypeGroup($dashboardGroup->identifier);
            }
        }
    }

    private function isInDashboardTreeRoot(Location $location): bool
    {
        return (new IsInDashboardTreeRoot(
            $this->configResolver,
            $this->locationHandler
        ))->isSatisfiedBy($location);
    }
}
