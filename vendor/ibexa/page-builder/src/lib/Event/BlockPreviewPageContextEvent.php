<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Event;

use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page;
use Ibexa\FieldTypePage\FieldType\Page\Block\Context\BlockContextInterface;
use Symfony\Contracts\EventDispatcher\Event;

class BlockPreviewPageContextEvent extends Event
{
    /** @var \Ibexa\FieldTypePage\FieldType\Page\Block\Context\BlockContextInterface */
    protected $blockContext;

    /** @var \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page */
    protected $page;

    /** @var array */
    protected $pagePreviewParameters;

    /**
     * @param \Ibexa\FieldTypePage\FieldType\Page\Block\Context\BlockContextInterface $blockContext
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page $page
     * @param array $pagePreviewParameters
     */
    public function __construct(
        BlockContextInterface $blockContext,
        Page $page,
        array $pagePreviewParameters
    ) {
        $this->blockContext = $blockContext;
        $this->page = $page;
        $this->pagePreviewParameters = $pagePreviewParameters;
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
}

class_alias(BlockPreviewPageContextEvent::class, 'EzSystems\EzPlatformPageBuilder\Event\BlockPreviewPageContextEvent');
