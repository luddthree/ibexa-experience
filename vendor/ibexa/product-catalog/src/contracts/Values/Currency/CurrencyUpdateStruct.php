<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Currency;

final class CurrencyUpdateStruct
{
    /** @var non-empty-string|null */
    private ?string $code = null;

    /** @phpstan-var int<0, max>|null */
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
     */
    public function setCode(?string $code): void
    {
        $this->code = $code;
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
     */
    public function setSubunits(?int $subunits): void
    {
        $this->subunits = $subunits;
    }

    /**
     * @phpstan-pure
     */
    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(?bool $enabled): void
    {
        $this->enabled = $enabled;
    }
}
