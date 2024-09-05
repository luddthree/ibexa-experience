<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

final class CurrencyUpdateStruct extends ValueObject
{
    /** @var non-empty-string|null */
    private ?string $code;

    /** @phpstan-var int<0, max>|null */
    private ?int $subunits;

    private ?bool $enabled;

    /**
     * @param non-empty-string|null $code
     *
     * @phpstan-param int<0, max>|null $subunits
     */
    public function __construct(?string $code, ?int $subunits, ?bool $enabled)
    {
        parent::__construct();

        $this->code = $code;
        $this->subunits = $subunits;
        $this->enabled = $enabled;
    }

    /**
     * @return non-empty-string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @phpstan-return int<0, max>|null
     */
    public function getSubUnits(): ?int
    {
        return $this->subunits;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }
}
