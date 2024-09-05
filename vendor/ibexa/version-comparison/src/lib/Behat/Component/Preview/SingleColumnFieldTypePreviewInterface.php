<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\VersionComparison\Behat\Component\Preview;

use Ibexa\Behat\Browser\Locator\LocatorInterface;

interface SingleColumnFieldTypePreviewInterface
{
    public function verifyRemovedData(string $expectedRemovedData): void;

    public function verifyAddedData(string $expectedAddedData): void;

    public function supports(string $fieldTypeIdentifier): bool;

    public function setParentLocator(LocatorInterface $locator): void;
}
