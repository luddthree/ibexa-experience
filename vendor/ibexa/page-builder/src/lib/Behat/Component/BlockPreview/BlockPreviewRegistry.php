<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\PageBuilder\Behat\Component\BlockPreview;

class BlockPreviewRegistry
{
    /** @var \Ibexa\PageBuilder\Behat\Component\BlockPreview\BlockPreviewInterface[] */
    private $blockPreviews;

    public function __construct(iterable $blockPreviews)
    {
        $this->blockPreviews = $blockPreviews;
    }

    public function getPreviewForBlock(string $blockType, string $layout): BlockPreviewInterface
    {
        foreach ($this->blockPreviews as $blockPreview) {
            if ($blockPreview->getSupportedBlockType() === $blockType && in_array($layout, $blockPreview->getSupportedLayouts(), true)) {
                return $blockPreview;
            }
        }

        throw new \Exception(sprintf(
            'Unsupported block type "%s". Ensure that %s exists for it.',
            $blockType,
            BlockPreviewInterface::class,
        ));
    }
}
