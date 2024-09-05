<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Behat\Component;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\ContentItemAdminPreview;
use Ibexa\AdminUi\Behat\Component\Fields\FieldTypeComponentInterface;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

class ProductPreview extends ContentItemAdminPreview
{
    /**
     * @param iterable<FieldTypeComponentInterface> $fieldTypeComponents
     */
    public function __construct(Session $session, iterable $fieldTypeComponents)
    {
        parent::__construct($session, $fieldTypeComponents);
        $this->locators->replace(new VisibleCSSLocator('nthFieldContainer', 'div.ibexa-pc-product-item-preview:nth-of-type(%s)'));
        $this->locators->replace(new VisibleCSSLocator('fieldName', '.ibexa-pc-product-item-preview__label'));
        $this->locators->replace(new VisibleCSSLocator('fieldValue', '.ibexa-pc-product-item-preview__value'));
    }
}
