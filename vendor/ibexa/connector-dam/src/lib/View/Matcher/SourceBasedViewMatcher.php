<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Connector\Dam\View\Matcher;

use Ibexa\Contracts\Connector\Dam\View\AssetView;
use Ibexa\Core\MVC\Symfony\Matcher\ViewMatcherInterface;
use Ibexa\Core\MVC\Symfony\View\View;

class SourceBasedViewMatcher implements ViewMatcherInterface
{
    /** @var string[] */
    private $sourceAssetsIdList;

    public function setMatchingConfig($matchingConfig)
    {
        if (!\is_array($matchingConfig)) {
            $matchingConfig = [$matchingConfig];
        }

        $this->sourceAssetsIdList = $matchingConfig;
    }

    public function match(View $view)
    {
        if (!$view instanceof AssetView) {
            return false;
        }

        return \in_array(
            $view->getAsset()->getSource()->getSourceIdentifier(),
            $this->sourceAssetsIdList,
            true
        );
    }
}

class_alias(SourceBasedViewMatcher::class, 'Ibexa\Platform\Connector\Dam\View\Matcher\SourceBasedViewMatcher');
