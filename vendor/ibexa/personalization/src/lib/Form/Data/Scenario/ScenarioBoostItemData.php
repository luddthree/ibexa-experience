<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Data\Scenario;

final class ScenarioBoostItemData
{
    /** @var bool */
    private $enabled;

    /** @var string|null */
    private $attribute;

    /** @var int|null */
    private $position;

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): ScenarioBoostItemData
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getAttribute(): ?string
    {
        return $this->attribute;
    }

    public function setAttribute(?string $attribute): ScenarioBoostItemData
    {
        $this->attribute = $attribute;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): ScenarioBoostItemData
    {
        $this->position = $position;

        return $this;
    }
}

class_alias(ScenarioBoostItemData::class, 'Ibexa\Platform\Personalization\Form\Data\Scenario\ScenarioBoostItemData');
