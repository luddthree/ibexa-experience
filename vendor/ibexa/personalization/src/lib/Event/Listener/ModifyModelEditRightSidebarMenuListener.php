<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Event\Listener;

use Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent;
use Ibexa\Personalization\Menu\ModelEditRightSidebarBuilder;
use Ibexa\Personalization\Service\ModelBuild\ModelBuildServiceInterface;
use Ibexa\Personalization\Value\ModelBuild\State;
use Symfony\Component\HttpFoundation\RequestStack;

final class ModifyModelEditRightSidebarMenuListener
{
    private const DISABLE_BUTTONS = [
        ModelEditRightSidebarBuilder::ITEM__SAVE,
        ModelEditRightSidebarBuilder::ITEM__SAVE_AND_CLOSE,
        ModelEditRightSidebarBuilder::ITEM__TRIGGER_MODEL_BUILD,
    ];

    private ModelBuildServiceInterface $modelBuildService;

    private RequestStack $requestStack;

    public function __construct(
        ModelBuildServiceInterface $modelBuildService,
        RequestStack $requestStack
    ) {
        $this->modelBuildService = $modelBuildService;
        $this->requestStack = $requestStack;
    }

    /**
     * @param \Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent $event
     */
    public function renderMenu(ConfigureMenuEvent $event): void
    {
        $menu = $event->getMenu();

        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return;
        }

        $requestAttributes = $request->attributes;
        if (
            !$requestAttributes->has('customerId')
            && !$requestAttributes->has('referenceCode')
        ) {
            return;
        }

        $customerId = (int) $requestAttributes->get('customerId');
        $referenceCode = $requestAttributes->get('referenceCode');

        $modelBuildStatus = $this->modelBuildService->getModelBuildStatus($customerId, $referenceCode);
        if (null === $modelBuildStatus) {
            return;
        }

        $lastBuildReport = $modelBuildStatus->getBuildReports()->getLastBuildReport();

        if (
            null === $lastBuildReport
            || !in_array($lastBuildReport->getState(), State::BUILD_IN_PROGRESS_STATES, true)) {
            return;
        }

        foreach ($menu->getChildren() as $identifier => $child) {
            if (in_array($identifier, self::DISABLE_BUTTONS, true)) {
                $child->setAttribute('disabled', true);
            }
        }
    }
}
