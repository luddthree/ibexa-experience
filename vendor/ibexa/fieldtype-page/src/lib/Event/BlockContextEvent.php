<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Event;

use Ibexa\FieldTypePage\FieldType\Page\Block\Context\BlockContextInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

class BlockContextEvent extends Event
{
    /** @var \Symfony\Component\HttpFoundation\Request */
    private $request;

    /** @var \Ibexa\FieldTypePage\FieldType\Page\Block\Context\BlockContextInterface|null */
    private $blockContext;

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Ibexa\FieldTypePage\FieldType\Page\Block\Context\BlockContextInterface $blockContext
     */
    public function __construct(
        Request $request,
        ?BlockContextInterface $blockContext = null
    ) {
        $this->request = $request;
        $this->blockContext = $blockContext;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }

    /**
     * @return \Ibexa\FieldTypePage\FieldType\Page\Block\Context\BlockContextInterface|null
     */
    public function getBlockContext(): ?BlockContextInterface
    {
        return $this->blockContext;
    }

    /**
     * @param \Ibexa\FieldTypePage\FieldType\Page\Block\Context\BlockContextInterface|null $blockContext
     */
    public function setBlockContext(?BlockContextInterface $blockContext): void
    {
        $this->blockContext = $blockContext;
    }
}

class_alias(BlockContextEvent::class, 'EzSystems\EzPlatformPageFieldType\Event\BlockContextEvent');
