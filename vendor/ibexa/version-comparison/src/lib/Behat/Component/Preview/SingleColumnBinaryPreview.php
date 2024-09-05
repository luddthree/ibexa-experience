<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\VersionComparison\Behat\Component\Preview;

use PHPUnit\Framework\Assert;

class SingleColumnBinaryPreview extends SingleColumnBaseFieldTypePreview
{
    public function supports(string $fieldTypeIdentifier): bool
    {
        return in_array($fieldTypeIdentifier, [
            'ezbinaryfile',
            'ezmedia',
        ]);
    }

    public function verifyAddedData(string $expectedAddedData): void
    {
        $values = $this->getAddedElements();
        $changedElement = $values[count($values) - 2];

        Assert::assertEquals($expectedAddedData, $changedElement);
    }

    public function verifyRemovedData(string $expectedRemovedData): void
    {
        $values = $this->getRemovedElements();
        $changedElement = $values[count($values) - 2];

        Assert::assertEquals($expectedRemovedData, $changedElement);
    }
}
