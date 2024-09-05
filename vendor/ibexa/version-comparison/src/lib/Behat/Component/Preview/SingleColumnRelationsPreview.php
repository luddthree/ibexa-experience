<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\VersionComparison\Behat\Component\Preview;

use PHPUnit\Framework\Assert;

class SingleColumnRelationsPreview extends SingleColumnBaseFieldTypePreview
{
    public function supports(string $fieldTypeIdentifier): bool
    {
        return in_array($fieldTypeIdentifier, [
            'ezobjectrelationlist',
            'ezobjectrelation',
        ]);
    }

    public function verifyAddedData(string $expectedAddedData): void
    {
        $changedElements = explode(' ', $this->getAddedElements()[0]);

        Assert::assertEquals($expectedAddedData, sprintf('%s %s', $changedElements[0], $changedElements[1]));
    }

    public function verifyRemovedData(string $expectedRemovedData): void
    {
        $changedElements = explode(' ', $this->getRemovedElements()[0]);

        Assert::assertEquals($expectedRemovedData, sprintf('%s %s', $changedElements[0], $changedElements[1]));
    }
}
