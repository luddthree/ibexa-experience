<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Siteaccess\Selector;

use Ibexa\Contracts\PageBuilder\Exception\NoMatchingSiteAccessFoundException;
use Ibexa\Contracts\PageBuilder\Siteaccess\Selector\Context;
use Ibexa\Contracts\PageBuilder\Siteaccess\Selector\SiteAccessSelector as SiteAccessSelectorInterface;

final class SiteAccessSelector implements SiteAccessSelectorInterface
{
    /** @var iterable<\Ibexa\Contracts\PageBuilder\Siteaccess\Selector\SelectorStrategy> */
    private iterable $strategies;

    public function __construct(
        iterable $strategies = []
    ) {
        $this->strategies = $strategies;
    }

    public function selectSiteAccess(Context $context, array $siteAccessNameList): string
    {
        foreach ($this->strategies as $strategy) {
            $siteAccess = $strategy->selectSiteAccess($context, $siteAccessNameList);

            if ($siteAccess !== null) {
                return $siteAccess;
            }
        }

        throw new NoMatchingSiteAccessFoundException();
    }
}
