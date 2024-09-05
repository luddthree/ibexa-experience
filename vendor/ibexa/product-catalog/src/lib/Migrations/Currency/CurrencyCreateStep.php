<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\Currency;

use Ibexa\Migration\ValueObject\Step\StepInterface;

final class CurrencyCreateStep implements StepInterface
{
    /** @phpstan-var non-empty-string */
    private string $code;

    /** @phpstan-var int<0, max> */
    private int $subunits;

    private bool $enabled;

    /**
     * @phpstan-param non-empty-string $code
     * @phpstan-param int<0, max> $subunits
     */
    public function __construct(string $code, int $subunits, bool $enabled)
    {
        $this->code = $code;
        $this->subunits = $subunits;
        $this->enabled = $enabled;
    }

    /**
     * @phpstan-return non-empty-string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @phpstan-return int<0, max>
     */
    public function getSubunits(): int
    {
        return $this->subunits;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
