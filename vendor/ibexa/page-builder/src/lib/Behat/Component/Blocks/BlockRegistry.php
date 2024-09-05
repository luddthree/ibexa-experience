<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\PageBuilder\Behat\Component\Blocks;

use Ibexa\Core\Base\Exceptions\InvalidArgumentException;

class BlockRegistry
{
    /** @var \Ibexa\PageBuilder\Behat\Component\Blocks\PageBuilderBlockInterface[] */
    private $blocks;

    public function __construct(iterable $blocks)
    {
        $this->blocks = $blocks;
    }

    public function getBlock(string $blockType): PageBuilderBlockInterface
    {
        $blockType = strtolower($blockType);

        $foundSupportedTypes = [];

        foreach ($this->blocks as $block) {
            $supportedType = strtolower($block->getBlockType());
            if ($supportedType === $blockType) {
                return $block;
            }

            $foundSupportedTypes[] = $supportedType;
        }

        throw new InvalidArgumentException(
            $blockType,
            sprintf(
                'There is no Blocks registered to handle the type: %s. Available ones are: %s',
                $blockType,
                implode(',', $foundSupportedTypes)
            )
        );
    }
}
