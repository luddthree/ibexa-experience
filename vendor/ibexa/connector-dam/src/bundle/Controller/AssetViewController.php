<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Connector\Dam\Controller;

use Ibexa\Connector\Dam\View\ForwardView;
use Ibexa\Core\MVC\Symfony\View\BaseView;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class AssetViewController extends AbstractController
{
    public function viewAction(BaseView $view)
    {
        if ($view instanceof ForwardView) {
            return $this->forward($view->getForwardTo(), $view->getParameters());
        }

        return $view;
    }
}

class_alias(AssetViewController::class, 'Ibexa\Platform\Bundle\Connector\Dam\Controller\AssetViewController');
