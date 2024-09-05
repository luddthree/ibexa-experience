<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Values;

final class AttributeDefinitionCreateStruct
{
    public string $identifier;

    public string $type;

    public int $attributeGroupId;

    public int $position;

    /**
     * @var array<string,mixed>
     */
    public array $options = [];

    /**
     * Human-readable name of the attribute definition.
     *
     * The structure of this field is:
     *
     * <code>
     * [2 => '<name_eng>', 4 => '<name_de>'];
     * </code>
     *
     * @var array<int,string>
     */
    public array $names = [];

    /**
     * Human-readable description of the attribute definition.
     *
     * The structure of this field is:
     *
     * <code>
     * [2 => '<description_eng>', 4 => '<description_de>'];
     * </code>
     *
     * @var array<int,string>
     */
    public array $descriptions = [];
}
