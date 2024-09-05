<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Event;

use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\FieldTypePage\FieldType\Page\Block\Context\BlockContextInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

class BlockResponseEvent extends Event
{
    /** @var \Ibexa\FieldTypePage\FieldType\Page\Block\Context\BlockContextInterface */
    protected $blockContext;

    /** @var \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue */
    protected $blockValue;

    /** @var \Symfony\Component\HttpFoundation\Request */
    protected $request;

    /** @var \Symfony\Component\HttpFoundation\Response */
    protected $response;

    /**
     * @param \Ibexa\FieldTypePage\FieldType\Page\Block\Context\BlockContextInterface $blockContext
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $blockValue
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    public function __construct(
        BlockContextInterface $blockContext,
        BlockValue $blockValue,
        Request $request,
        Response $response
    ) {
        $this->blockContext = $blockContext;
        $this->blockValue = $blockValue;
        $this->request = $request;
        $this->response = $response;
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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }
}

class_alias(BlockResponseEvent::class, 'EzSystems\EzPlatformPageFieldType\Event\BlockResponseEvent');
