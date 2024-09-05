<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Twig;

use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\RenderRequestInterface;

class TwigRenderRequest implements RenderRequestInterface
{
    /** @var string */
    private $template;

    /** @var array<string, mixed> */
    private $parameters;

    /**
     * @param string $template
     * @phpstan-param array<string, mixed> $parameters
     */
    public function __construct($template, array $parameters)
    {
        $this->template = $template;
        $this->parameters = $parameters;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function setTemplate(string $template)
    {
        $this->template = $template;
    }

    /**
     * @phpstan-return array<string, mixed>
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @phpstan-param array<string, mixed> $parameters
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @param mixed $parameter
     */
    public function addParameter(string $name, $parameter): void
    {
        $this->parameters[$name] = $parameter;
    }
}

class_alias(TwigRenderRequest::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Block\Renderer\Twig\TwigRenderRequest');
