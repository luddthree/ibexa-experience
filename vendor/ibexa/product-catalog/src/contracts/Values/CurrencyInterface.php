<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values;

interface CurrencyInterface
{
    public function getId(): int;

    /**
     * @phpstan-return non-empty-string
     */
    public function getCode(): string;

    /**
     * @phpstan-return int<0, max>
     */
    public function getSubUnits(): int;

    public function isEnabled(): bool;
}
