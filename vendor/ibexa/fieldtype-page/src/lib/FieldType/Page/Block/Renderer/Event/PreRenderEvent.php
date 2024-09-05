<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event;

use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\FieldTypePage\FieldType\Page\Block\Context\BlockContextInterface;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\RenderRequestInterface;
use Symfony\Contracts\EventDispatcher\Event;

class PreRenderEvent extends Event
{
    /** @var \Ibexa\FieldTypePage\FieldType\Page\Block\Context\BlockContextInterface */
    private $blockContext;

    /** @var \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue */
    private $blockValue;

    /** @var \Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\RenderRequestInterface */
    private $renderRequest;

    /**
     * @param \Ibexa\FieldTypePage\FieldType\Page\Block\Context\BlockContextInterface $blockContext
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $blockValue
     * @param \Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\RenderRequestInterface $renderRequest
     */
    public function __construct(
        BlockContextInterface $blockContext,
        BlockValue $blockValue,
        RenderRequestInterface $renderRequest
    ) {
        $this->blockContext = $blockContext;
        $this->blockValue = $blockValue;
        $this->renderRequest = $renderRequest;
    }

    /**
     * @return \Ibexa\FieldTypePage\FieldType\Page\Block\Context\BlockContextInterface
     */
    public function getBlockContext(): BlockContextInterface
    {
        return $this->blockContext;
    }

    /**
     * @param \Ibexa\FieldTypePage\FieldType\Page\Block\Context\BlockContextInterface $blockContext
     */
    public function setBlockContext(BlockContextInterface $blockContext): void
    {
        $this->blockContext = $blockContext;
    }

    /**
     * @return \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue
     */
    public function getBlockValue(): BlockValue
    {
        return $this->blockValue;
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $blockValue
     */
    public function setBlockValue(BlockValue $blockValue): void
    {
        $this->blockValue = $blockValue;
    }

    /**
     * @return \Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\RenderRequestInterface
     */
    public function getRenderRequest(): RenderRequestInterface
    {
        return $this->renderRequest;
    }

    /**
     * @param \Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\RenderRequestInterface $renderRequest
     */
    public function setRenderRequest(RenderRequestInterface $renderRequest)
    {
        $this->renderRequest = $renderRequest;
    }
}

class_alias(PreRenderEvent::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Block\Renderer\Event\PreRenderEvent');
