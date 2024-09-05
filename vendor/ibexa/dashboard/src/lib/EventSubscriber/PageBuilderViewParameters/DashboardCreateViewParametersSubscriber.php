<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Dashboard\EventSubscriber\PageBuilderViewParameters;

use Ibexa\ContentForms\Content\View\ContentCreateView;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Core\MVC\Symfony\View\View;

/**
 * @internal
 */
final class DashboardCreateViewParametersSubscriber extends BaseDashboardViewParametersSubscriber
{
    protected function getContentTypeFromView(View $view): ?ContentType
    {
        return $view instanceof ContentCreateView ? $view->getContentType() : null;
    }
}
