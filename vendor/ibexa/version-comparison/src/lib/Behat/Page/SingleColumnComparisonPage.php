<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\VersionComparison\Behat\Page;

use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

final class SingleColumnComparisonPage extends BaseComparisonPage
{
    public function verifyDataAdded(string $fieldName, string $fieldTypeIdentifier, string $expectedValueAdded)
    {
        $this->versionPreview->verifyAddedFieldData($fieldName, $fieldTypeIdentifier, $expectedValueAdded);
    }

    public function verifyDataRemoved(string $fieldName, string $fieldTypeIdentifier, $expectedValueAddedRemoved)
    {
        $this->versionPreview->verifyRemovedFieldData($fieldName, $fieldTypeIdentifier, $expectedValueAddedRemoved);
    }

    public function verifyIsLoaded(): void
    {
        parent::verifyIsLoaded();
        $this->getHTMLPage()->findAll(new VisibleCSSLocator('dataPanel', '.ibexa-version-compare__content'))->assert()->countEquals(1);
    }

    public function getName(): string
    {
        return 'Single column version comparison';
    }

    protected function getRoute(): string
    {
        return sprintf(
            'version/comparison/%d/%d/%d',
            $this->expectedContentId,
            $this->expectedRightVersionNumber,
            $this->expectedLeftVersionNumber
        );
    }
}
