<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Dashboard\Menu\Voter;

use Ibexa\Bundle\Dashboard\Menu\ConfigureMainMenuListener;
use Ibexa\Bundle\Dashboard\ViewBuilder\DashboardViewBuilder;
use Ibexa\Core\MVC\Symfony\View\ContentView;
use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\Voter\VoterInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class DashboardVoter implements VoterInterface
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function matchItem(ItemInterface $item): ?bool
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return null;
        }

        $contentView = $request->attributes->get('view');
        if (
            !$contentView instanceof ContentView
            || $contentView->getViewType() !== DashboardViewBuilder::DASHBOARD_VIEW_TYPE
        ) {
            return null;
        }

        if ($item->getName() === ConfigureMainMenuListener::ITEM_ADMIN__DASHBOARD) {
            return false;
        }

        return null;
    }
}
