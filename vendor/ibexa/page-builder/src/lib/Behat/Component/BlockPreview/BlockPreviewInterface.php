<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\PageBuilder\Behat\Component\BlockPreview;

interface BlockPreviewInterface
{
    public function setExpectedData(array $previewData): void;

    public function verifyPreview(): void;

    public function getSupportedBlockType(): string;

    public function getSupportedLayouts(): array;
}
