<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Connector\Dam\View;

use Ibexa\Core\MVC\Symfony\Matcher\MatcherFactoryInterface;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\Core\MVC\Symfony\View\ViewProvider;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

final class AssetViewProvider implements ViewProvider
{
    /** @var \Ibexa\Core\MVC\Symfony\Matcher\MatcherFactoryInterface */
    private $matcherFactory;

    public function __construct(MatcherFactoryInterface $matcherFactory)
    {
        $this->matcherFactory = $matcherFactory;
    }

    public function getView(View $view)
    {
        if (($configHash = $this->matcherFactory->match($view)) === null) {
            return null;
        }

        return $this->buildAssetView($configHash);
    }

    private function buildAssetView(array $viewConfig): AssetView
    {
        $view = new AssetView();

        if (isset($viewConfig['template'])) {
            $view->setTemplateIdentifier($viewConfig['template']);
        }

        if (isset($viewConfig['controller'])) {
            $view->setControllerReference(new ControllerReference($viewConfig['controller']));
        }

        if (isset($viewConfig['params']) && \is_array($viewConfig['params'])) {
            $view->addParameters($viewConfig['params']);
        }

        return $view;
    }
}

class_alias(AssetViewProvider::class, 'Ibexa\Platform\Connector\Dam\View\AssetViewProvider');
