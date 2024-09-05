<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Behat\Page;

use Ibexa\AdminUi\Behat\Component\Fields\FieldTypeComponent;
use Ibexa\AdminUi\Behat\Page\ContentUpdateItemPage;
use Ibexa\Behat\Browser\Locator\LocatorInterface;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

final class ProductCreatePage extends ContentUpdateItemPage
{
    protected function specifyLocators(): array
    {
        $replacements = [
            new VisibleCSSLocator('nthField', 'div.ibexa-attribute-edit:nth-of-type(%s)'),
            new VisibleCSSLocator('fieldLabel', '.ibexa-attribute-edit .ibexa-attribute-edit__label'),
            new VisibleCSSLocator('section', '[data-id="%1$s"] .ibexa-attribute-edit .ibexa-attribute-edit__label, [data-id="%1$s"] .ibexa-data-source__field .ibexa-data-source__label'),
        ];

        $replacementsIdentifiers = array_map(static function (LocatorInterface $locator): string {
            return $locator->getIdentifier();
        }, $replacements);

        $parentLocatorsWithoutReplecements = array_filter(
            parent::specifyLocators(),
            static function (LocatorInterface $locator) use ($replacementsIdentifiers): bool {
                return !in_array($locator->getIdentifier(), $replacementsIdentifiers);
            }
        );

        return array_merge($parentLocatorsWithoutReplecements, $replacements);
    }

    /**
     * @param array<string,string> $value
     */
    public function fillAttributeWithValue(string $label, array $value, string $attributeType): void
    {
        $this->getAttribute($label, $attributeType)->setValue($value);
    }

    public function getAttribute(string $attributeName, string $attributeType): FieldTypeComponent
    {
        $fieldLocator = new VisibleCSSLocator('', sprintf($this->getLocator('nthField')->getSelector(), $this->getFieldPosition($attributeName)));
        foreach ($this->fieldTypeComponents as $fieldTypeComponent) {
            if ($fieldTypeComponent->getFieldTypeIdentifier() === $attributeType) {
                $fieldTypeComponent->setParentLocator($fieldLocator);

                return $fieldTypeComponent;
            }
        }
        throw new \Exception(sprintf('Attribute %s is not found', $attributeType));
    }
}
