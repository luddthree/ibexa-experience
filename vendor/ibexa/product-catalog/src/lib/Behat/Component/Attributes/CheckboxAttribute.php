<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Behat\Component\Attributes;

use Ibexa\AdminUi\Behat\Component\Fields\FieldTypeComponent;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

final class CheckboxAttribute extends FieldTypeComponent
{
    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('fieldInput', '.ibexa-input'),
        ];
    }

    public function getFieldTypeIdentifier(): string
    {
        return 'checkbox';
    }
}
