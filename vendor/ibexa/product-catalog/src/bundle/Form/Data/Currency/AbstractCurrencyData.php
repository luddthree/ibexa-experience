<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data\Currency;

use Ibexa\Bundle\ProductCatalog\Validator\Constraints\UniqueCurrencyCode;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @UniqueCurrencyCode
 */
abstract class AbstractCurrencyData
{
    /**
     * @var non-empty-string|null
     *
     * @Assert\NotBlank
     * @Assert\Length(min=1, max=3)
     */
    private ?string $code = null;

    /**
     * @phpstan-var int<0, max>|null
     *
     * @Assert\PositiveOrZero()
     * @Assert\LessThanOrEqual(4)
     * @Assert\NotBlank
     */
    private ?int $subunits = null;

    private ?bool $enabled = null;

    /**
     * @phpstan-pure
     *
     * @phpstan-return non-empty-string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @phpstan-param non-empty-string|null $code
     *
     * @return $this
     */
    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @phpstan-pure
     *
     * @phpstan-return int<0, max>|null
     */
    public function getSubunits(): ?int
    {
        return $this->subunits;
    }

    /**
     * @phpstan-param int<0, max>|null $subunits
     *
     * @phpstan-return $this
     */
    public function setSubunits(?int $subunits): self
    {
        $this->subunits = $subunits;

        return $this;
    }

    /**
     * @phpstan-pure
     */
    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    /**
     * @phpstan-return $this
     */
    public function setEnabled(?bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }
}
