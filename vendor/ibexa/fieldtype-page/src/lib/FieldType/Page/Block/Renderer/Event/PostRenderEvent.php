<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event;

use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\FieldTypePage\FieldType\Page\Block\Context\BlockContextInterface;
use Symfony\Contracts\EventDispatcher\Event;

class PostRenderEvent extends Event
{
    /** @var \Ibexa\FieldTypePage\FieldType\Page\Block\Context\BlockContextInterface */
    private $blockContext;

    /** @var \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue */
    private $blockValue;

    /** @var string */
    private $renderedBlock;

    /**
     * @param \Ibexa\FieldTypePage\FieldType\Page\Block\Context\BlockContextInterface $blockContext
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $blockValue
     * @param string $renderedBlock
     */
    public function __construct(
        BlockContextInterface $blockContext,
        BlockValue $blockValue,
        string $renderedBlock
    ) {
        $this->blockContext = $blockContext;
        $this->blockValue = $blockValue;
        $this->renderedBlock = $renderedBlock;
    }

    /**
     * @return \Ibexa\FieldTypePage\FieldType\Page\Block\Context\BlockContextInterface
     */
    public function getBlockContext(): BlockContextInterface
    {
        return $this->blockContext;
    }

    /**
     * @return \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue
     */
    public function getBlockValue(): BlockValue
    {
        return $this->blockValue;
    }

    /**
     * @return string
     */
    public function getRenderedBlock(): string
    {
        return $this->renderedBlock;
    }

    /**
     * @param string $renderedBlock
     */
    public function setRenderedBlock(string $renderedBlock)
    {
        $this->renderedBlock = $renderedBlock;
    }
}

class_alias(PostRenderEvent::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Block\Renderer\Event\PostRenderEvent');
