<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Values;

use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;

final class Currency implements CurrencyInterface
{
    private int $id;

    /** @var non-empty-string */
    private string $code;

    /** @phpstan-var int<0, max> */
    private int $subunits;

    private bool $enabled;

    /**
     * @param non-empty-string $code
     *
     * @phpstan-param int<0, max> $subunits
     */
    public function __construct(int $id, string $code, int $subunits, bool $enabled)
    {
        $this->id = $id;
        $this->code = $code;
        $this->subunits = $subunits;
        $this->enabled = $enabled;
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return non-empty-string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @phpstan-return int<0, max>
     */
    public function getSubUnits(): int
    {
        return $this->subunits;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
