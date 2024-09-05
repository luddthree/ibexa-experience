<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\PageBuilder\Event;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Symfony\Contracts\EventDispatcher\Event;

final class GenerateContentPreviewUrlEvent extends Event
{
    public const NAME = 'ibexa.page_builder.generate_content_preview_url';

    private Content $content;

    private string $routeName;

    /** @var array<mixed> */
    private array $parameters;

    private int $referenceType;

    /**
     * @param array<mixed> $parameters
     */
    public function __construct(Content $content, string $routeName, array $parameters, int $referenceType)
    {
        $this->content = $content;
        $this->routeName = $routeName;
        $this->parameters = $parameters;
        $this->referenceType = $referenceType;
    }

    public function getContent(): Content
    {
        return $this->content;
    }

    public function setContent(Content $content): void
    {
        $this->content = $content;
    }

    public function getRouteName(): string
    {
        return $this->routeName;
    }

    public function setRouteName(string $routeName): void
    {
        $this->routeName = $routeName;
    }

    /**
     * @return array<mixed>
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param array<mixed> $parameters
     */
    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    public function addParameter(string $parameterName, string $parameterValue): void
    {
        $this->parameters[$parameterName] = $parameterValue;
    }

    public function getReferenceType(): int
    {
        return $this->referenceType;
    }

    public function setReferenceType(int $referenceType): void
    {
        $this->referenceType = $referenceType;
    }
}
