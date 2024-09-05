<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\VersionComparison\Behat\Component\Preview;

use PHPUnit\Framework\Assert;

class SingleColumnImagePreview extends SingleColumnBaseFieldTypePreview
{
    public function supports(string $fieldTypeIdentifier): bool
    {
        return in_array($fieldTypeIdentifier, [
            'ezimage',
            'ezimageasset',
        ]);
    }

    public function verifyAddedData(string $expectedAddedData): void
    {
        $values = $this->getAddedElements();
        $changedElement = end($values);

        Assert::assertEquals($expectedAddedData, $changedElement);
    }

    public function verifyRemovedData(string $expectedRemovedData): void
    {
        $values = $this->getRemovedElements();
        $changedElement = end($values);

        Assert::assertEquals($expectedRemovedData, $changedElement);
    }
}
