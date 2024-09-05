<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Connect\PageBuilder;

final class Template
{
    /** @var non-empty-string */
    private string $id;

    /** @var non-empty-string */
    private string $label;

    /** @var non-empty-string */
    private string $template;

    /** @var \Ibexa\Connect\PageBuilder\Template\Parameter[] */
    private array $parameters;

    /**
     * @param non-empty-string $id
     * @param non-empty-string $label
     * @param non-empty-string $template
     * @param array<\Ibexa\Connect\PageBuilder\Template\Parameter> $parameters
     */
    public function __construct(
        string $id,
        string $label,
        string $template,
        array $parameters
    ) {
        $this->id = $id;
        $this->label = $label;
        $this->template = $template;
        $this->parameters = $parameters;
    }

    /**
     * @phpstan-return non-empty-string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @phpstan-return non-empty-string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @phpstan-return non-empty-string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @return \Ibexa\Connect\PageBuilder\Template\Parameter[]
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}
