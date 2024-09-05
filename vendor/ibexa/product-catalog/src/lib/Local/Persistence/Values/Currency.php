<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Values;

use Ibexa\Contracts\Core\Persistence\ValueObject;

final class Currency extends ValueObject
{
    public int $id;

    /**
     * @phpstan-var non-empty-string
     */
    public string $code;

    /**
     * @phpstan-var int<0, max>
     */
    public int $subunits;

    public bool $enabled;

    /**
     * @phpstan-param non-empty-string $code
     * @phpstan-param int<0, max> $subunits
     */
    public function __construct(int $id, string $code, int $subunits, bool $enabled)
    {
        parent::__construct();
        $this->code = $code;
        $this->subunits = $subunits;
        $this->id = $id;
        $this->enabled = $enabled;
    }
}
