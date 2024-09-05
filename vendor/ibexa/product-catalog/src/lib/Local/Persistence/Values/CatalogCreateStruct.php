<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Values;

use Ibexa\Contracts\Core\Persistence\ValueObject;

final class CatalogCreateStruct extends ValueObject
{
    public string $identifier;

    public string $status;

    /**
     * Human-readable name of the catalog.
     *
     * The structure of this field is:
     *
     * <code>
     * [2 => '<name_eng>', 4 => '<name_de>'];
     * </code>
     *
     * @var array<int,string>
     */
    public array $names;

    /**
     * Human-readable name of the catalog.
     *
     * The structure of this field is:
     *
     * <code>
     * [2 => '<name_eng>', 4 => '<name_de>'];
     * </code>
     *
     * @var array<int,string>
     */
    public array $descriptions;

    public int $creatorId;

    public string $query;
}
