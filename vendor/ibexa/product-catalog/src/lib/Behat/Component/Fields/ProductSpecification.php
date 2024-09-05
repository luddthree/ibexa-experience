<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Behat\Component\Fields;

use Ibexa\AdminUi\Behat\Component\Fields\FieldTypeComponent;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

class ProductSpecification extends FieldTypeComponent
{
    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('codeInput', '.ibexa-data-source__field--code input'),
        ];
    }

    public function getFieldTypeIdentifier(): string
    {
        return 'ibexa_product_specification';
    }

    /**
     * @param array<string,string> $parameters
     */
    public function setValue(array $parameters): void
    {
        $this->getHTMLPage()->find($this->parentLocator)->find($this->getLocator('codeInput'))->setValue($parameters['Code']);
    }
}
