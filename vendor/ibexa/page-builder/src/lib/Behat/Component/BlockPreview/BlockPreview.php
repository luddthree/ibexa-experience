<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\PageBuilder\Behat\Component\BlockPreview;

use Ibexa\Behat\Browser\Component\Component;

abstract class BlockPreview extends Component implements BlockPreviewInterface
{
    public function verifyIsLoaded(): void
    {
    }

    public function getSupportedLayouts(): array
    {
        return ['default'];
    }
}
