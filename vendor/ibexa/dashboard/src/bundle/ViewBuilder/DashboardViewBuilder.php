<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Dashboard\ViewBuilder;

use Ibexa\Bundle\Dashboard\Controller\DashboardController;
use Ibexa\Core\MVC\Symfony\View\Builder\ContentViewBuilder;
use Ibexa\Core\MVC\Symfony\View\Builder\ViewBuilder;
use Ibexa\Core\MVC\Symfony\View\View;

final class DashboardViewBuilder implements ViewBuilder
{
    public const DASHBOARD_VIEW_TYPE = 'dashboard';

    private ContentViewBuilder $contentViewBuilder;

    public function __construct(
        ContentViewBuilder $contentViewBuilder
    ) {
        $this->contentViewBuilder = $contentViewBuilder;
    }

    public function matches($argument): bool
    {
        return $argument === DashboardController::class . '::dashboardAction';
    }

    /**
     * @param array<string,mixed> $parameters
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function buildView(array $parameters): View
    {
        $parameters['viewType'] = self::DASHBOARD_VIEW_TYPE;

        return $this->contentViewBuilder->buildView($parameters);
    }
}
