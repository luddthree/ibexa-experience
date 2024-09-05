<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Behat\Page;

use Ibexa\AdminUi\Behat\Page\AdminUpdateItemPage;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

final class CurrencyUpdatePage extends AdminUpdateItemPage
{
    public function enableCurrency(): void
    {
        $this->getHTMLPage()->find($this->getLocator('checkbox'))->click();
    }

    protected function specifyLocators(): array
    {
        return array_merge(
            [
                new VisibleCSSLocator('checkbox', '.ibexa-toggle--checkbox'),
            ],
            parent::specifyLocators()
        );
    }
}
