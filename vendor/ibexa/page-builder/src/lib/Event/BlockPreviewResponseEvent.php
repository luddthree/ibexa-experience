<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Event;

use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page;
use Ibexa\FieldTypePage\FieldType\Page\Block\Context\BlockContextInterface;
use Symfony\Contracts\EventDispatcher\Event;

class BlockPreviewResponseEvent extends Event
{
    /** @var \Ibexa\FieldTypePage\FieldType\Page\Block\Context\BlockContextInterface */
    protected $blockContext;

    /** @var array */
    protected $pagePreviewParameters;

    /** @var \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page */
    protected $page;

    /** @var \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue */
    protected $blockValue;

    /** @var array */
    protected $responseData;

    /**
     * @param \Ibexa\FieldTypePage\FieldType\Page\Block\Context\BlockContextInterface $blockContext
     * @param array $pagePreviewParameters
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page $page
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $blockValue
     * @param array $responseData
     */
    public function __construct(
        BlockContextInterface $blockContext,
        array $pagePreviewParameters,
        Page $page,
        BlockValue $blockValue,
        array $responseData = []
    ) {
        $this->blockContext = $blockContext;
        $this->pagePreviewParameters = $pagePreviewParameters;
        $this->page = $page;
        $this->blockValue = $blockValue;
        $this->responseData = $responseData;
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
     * @return array
     */
    public function getPagePreviewParameters(): array
    {
        return $this->pagePreviewParameters;
    }

    /**
     * @param array $pagePreviewParameters
     */
    public function setPagePreviewParameters(array $pagePreviewParameters): void
    {
        $this->pagePreviewParameters = $pagePreviewParameters;
    }

    /**
     * @return \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page
     */
    public function getPage(): Page
    {
        return $this->page;
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page $page
     */
    public function setPage(Page $page): void
    {
        $this->page = $page;
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
     * @return array
     */
    public function getResponseData(): array
    {
        return $this->responseData;
    }

    /**
     * @param array $responseData
     */
    public function setResponseData(array $responseData): void
    {
        $this->responseData = $responseData;
    }
}

class_alias(BlockPreviewResponseEvent::class, 'EzSystems\EzPlatformPageBuilder\Event\BlockPreviewResponseEvent');
