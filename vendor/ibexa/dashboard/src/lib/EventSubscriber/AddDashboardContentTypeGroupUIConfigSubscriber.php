<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Dashboard\EventSubscriber;

use Ibexa\AdminUi\Event\AddContentTypeGroupToUIConfigEvent;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class AddDashboardContentTypeGroupUIConfigSubscriber implements EventSubscriberInterface
{
    public const CONTENT_TYPE_IDENTIFIER_PARAM_NAME = 'dashboard.content_type_group_identifier';

    private ConfigResolverInterface $configResolver;

    private ContentTypeService $contentTypeService;

    public function __construct(
        ConfigResolverInterface $configResolver,
        ContentTypeService $contentTypeService
    ) {
        $this->configResolver = $configResolver;
        $this->contentTypeService = $contentTypeService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AddContentTypeGroupToUIConfigEvent::class => ['addDashboardContentTypeGroup'],
        ];
    }

    public function addDashboardContentTypeGroup(AddContentTypeGroupToUIConfigEvent $event): void
    {
        $dashboardContentTypeGroupIdentifier = $this->configResolver->getParameter(
            self::CONTENT_TYPE_IDENTIFIER_PARAM_NAME,
        );

        try {
            $dashboardContentTypeGroup = $this->contentTypeService->loadContentTypeGroupByIdentifier(
                $dashboardContentTypeGroupIdentifier
            );
        } catch (NotFoundException $e) {
            return;
        }

        $event->addContentTypeGroup($dashboardContentTypeGroup);
    }
}
