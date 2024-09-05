<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Connect\PageBuilder\Template;

final class Parameter
{
    /** @var non-empty-string */
    private string $name;

    /** @var non-empty-string */
    private string $label;

    /** @var non-empty-string */
    private string $type;

    private bool $required;

    /** @phpstan-var array<mixed> */
    private array $options;

    /**
     * @phpstan-param non-empty-string $name
     * @phpstan-param non-empty-string $label
     * @phpstan-param non-empty-string $type
     * @phpstan-param array<mixed> $options
     */
    public function __construct(
        string $name,
        string $label,
        string $type,
        bool $required,
        array $options = []
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->type = $type;
        $this->required = $required;
        $this->options = $options;
    }

    /**
     * @phpstan-return non-empty-string
     */
    public function getName(): string
    {
        return $this->name;
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
    public function getType(): string
    {
        return $this->type;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * @phpstan-return array<mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
