<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Behat\Component;

use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Element\Criterion\ElementTextCriterion;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

final class CategoryFilter extends Component
{
    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->find($this->getLocator('category'))->assert()->isVisible();
    }

    public function changeCategoryFilter(string $filterName): void
    {
        $this->getHTMLPage()->setTimeout(5)->findAll($this->getLocator('filters'))
            ->getByCriterion(new ElementTextCriterion($filterName))->click();
    }

    /**
     * @return \Ibexa\Behat\Browser\Locator\LocatorInterface[]
     */
    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('category', '.c-tb-header__name'),
            new VisibleCSSLocator('filters', '.c-tb-list'),
        ];
    }
}
