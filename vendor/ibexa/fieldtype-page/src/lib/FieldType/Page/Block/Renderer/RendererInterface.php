<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\Page\Block\Renderer;

/**
 * Renderer interface used for rendering landing page blocks.
 */
interface RendererInterface
{
    /**
     * Returns true if renderer is able to render passed $renderRequest.
     *
     * @param RenderRequestInterface $renderRequest
     *
     * @return bool
     */
    public function supports(RenderRequestInterface $renderRequest): bool;

    /**
     * Renders passed $renderRequest.
     *
     * @param RenderRequestInterface $renderRequest
     *
     * @return string
     */
    public function render(RenderRequestInterface $renderRequest): string;
}

class_alias(RendererInterface::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Block\Renderer\RendererInterface');
