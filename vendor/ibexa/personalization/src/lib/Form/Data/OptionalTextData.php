<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Data;

final class OptionalTextData
{
    /** @var bool */
    private $enabled;

    /** @var string|null */
    private $value;

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): OptionalTextData
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): OptionalTextData
    {
        $this->value = $value;

        return $this;
    }
}

class_alias(OptionalTextData::class, 'Ibexa\Platform\Personalization\Form\Data\OptionalTextData');
