<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\VersionComparison\Behat\Component;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\ContentItemAdminPreview;
use Ibexa\Behat\Browser\Locator\CSSLocatorBuilder;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Traversable;

class VersionPreview extends ContentItemAdminPreview
{
    /** @var \Ibexa\VersionComparison\Behat\Component\Preview\SingleColumnFieldTypePreviewInterface[] */
    private iterable $changedFieldComponents;

    private VisibleCSSLocator $rightNthFieldContainer;

    private VisibleCSSLocator $leftNthFieldContainer;

    private VisibleCSSLocator $singleColumnFieldContainer;

    private VisibleCSSLocator $rightNthFieldContainerColumn;

    private VisibleCSSLocator $leftNthFieldContainerColumn;

    public function __construct(Session $session, Traversable $fieldTypeComponents, iterable $changedFieldComponents)
    {
        parent::__construct($session, $fieldTypeComponents);
        $this->rightNthFieldContainer = new VisibleCSSLocator('nthFieldContainer', 'div.ibexa-version-compare__field-wrapper:nth-of-type(%s) div.ibexa-content-field:nth-of-type(2)');
        $this->rightNthFieldContainerColumn = new VisibleCSSLocator('fieldName', '.ibexa-version-compare__field-wrapper div.ibexa-content-field:nth-of-type(2) .ibexa-content-field__name');

        $this->leftNthFieldContainer = new VisibleCSSLocator('nthFieldContainer', 'div.ibexa-version-compare__field-wrapper:nth-of-type(%s) div.ibexa-content-field:nth-of-type(1)');
        $this->leftNthFieldContainerColumn = new VisibleCSSLocator('fieldName', '.ibexa-version-compare__field-wrapper div.ibexa-content-field:nth-of-type(1) .ibexa-content-field__name');

        $this->singleColumnFieldContainer = new VisibleCSSLocator('nthFieldContainer', '.ibexa-version-compare div.ibexa-version-compare__field-wrapper:nth-of-type(%s) .ibexa-content-field');
        $this->changedFieldComponents = $changedFieldComponents;
    }

    public function verifyRightSideFieldHasCorrectValues(string $fieldName, array $expectedValue, string $fieldTypeIdentifier)
    {
        $this->locators->replace($this->rightNthFieldContainer);
        $this->locators->replace($this->rightNthFieldContainerColumn);
        $this->verifyFieldHasValues($fieldName, $expectedValue, $fieldTypeIdentifier);
    }

    public function verifyLeftSideFieldHasCorrectValues(string $fieldName, array $expectedValue, string $fieldTypeIdentifier)
    {
        $this->locators->replace($this->leftNthFieldContainer);
        $this->locators->replace($this->leftNthFieldContainerColumn);
        $this->verifyFieldHasValues($fieldName, $expectedValue, $fieldTypeIdentifier);
    }

    public function verifyAddedFieldData(string $fieldName, string $fieldTypeIdentifier, string $expectedValueAdded): void
    {
        $this->locators->replace($this->singleColumnFieldContainer);

        $nthFieldLocator = new VisibleCSSLocator('', sprintf($this->getLocator('nthFieldContainer')->getSelector(), $this->getFieldPosition($fieldName)));
        $fieldValueLocator = CSSLocatorBuilder::base($nthFieldLocator)->withDescendant($this->getLocator('fieldValue'))->build();

        foreach ($this->changedFieldComponents as $changedFieldComponent) {
            if ($changedFieldComponent->supports($fieldTypeIdentifier)) {
                $changedFieldComponent->setParentLocator($fieldValueLocator);
                $changedFieldComponent->verifyAddedData($expectedValueAdded);

                return;
            }
        }

        throw new \Exception(sprintf('Field: %s is not supported for Version Comparison', $fieldTypeIdentifier));
    }

    public function verifyRemovedFieldData(string $fieldName, string $fieldTypeIdentifier, string $expectedValueRemoved): void
    {
        $this->locators->replace($this->singleColumnFieldContainer);

        $nthFieldLocator = new VisibleCSSLocator('', sprintf($this->getLocator('nthFieldContainer')->getSelector(), $this->getFieldPosition($fieldName)));
        $fieldValueLocator = CSSLocatorBuilder::base($nthFieldLocator)->withDescendant($this->getLocator('fieldValue'))->build();

        foreach ($this->changedFieldComponents as $changedFieldComponent) {
            if ($changedFieldComponent->supports($fieldTypeIdentifier)) {
                $changedFieldComponent->setParentLocator($fieldValueLocator);
                $changedFieldComponent->verifyRemovedData($expectedValueRemoved);

                return;
            }
        }

        throw new \Exception(sprintf('Field: %s is not supported for Version Comparison', $fieldTypeIdentifier));
    }
}
