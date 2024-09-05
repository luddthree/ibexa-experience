<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Data\Scenario;

final class ScenarioExcludedCategoriesData
{
    private bool $enabled;

    /** @var array<string> */
    private array $paths;

    /**
     * @param array<string> $paths
     */
    public function __construct(
        bool $enabled,
        array $paths = []
    ) {
        $this->enabled = $enabled;
        $this->paths = $paths;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getPaths(): array
    {
        return $this->paths;
    }

    /**
     * @param array<string> $paths
     */
    public function setPaths(array $paths): self
    {
        $this->paths = $paths;

        return $this;
    }
}
