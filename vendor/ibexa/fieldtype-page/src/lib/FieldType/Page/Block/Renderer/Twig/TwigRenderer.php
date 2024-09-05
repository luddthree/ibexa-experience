<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Twig;

use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\RendererInterface;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\RenderRequestInterface;
use Twig\Environment;

class TwigRenderer implements RendererInterface
{
    /** @var \Twig\Environment */
    private $templatingEngine;

    /**
     * @param \Twig\Environment $templatingEngine
     */
    public function __construct(Environment $templatingEngine)
    {
        $this->templatingEngine = $templatingEngine;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(RenderRequestInterface $renderRequest): bool
    {
        return $renderRequest instanceof TwigRenderRequest;
    }

    /**
     * {@inheritdoc}
     *
     * @param \Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\RenderRequestInterface|\Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Twig\TwigRenderRequest $renderRequest
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function render(RenderRequestInterface $renderRequest): string
    {
        if (!$renderRequest instanceof TwigRenderRequest) {
            throw new \InvalidArgumentException('Invalid $renderRequest passed. TwigRenderer can only render TwigRenderRequest objects.');
        }

        return $this->templatingEngine->render($renderRequest->getTemplate(), $renderRequest->getParameters());
    }
}

class_alias(TwigRenderer::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Block\Renderer\Twig\TwigRenderer');
