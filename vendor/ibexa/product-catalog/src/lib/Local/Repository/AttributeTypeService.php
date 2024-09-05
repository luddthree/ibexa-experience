<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository;

use Ibexa\Contracts\ProductCatalog\AttributeTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeTypeInterface;
use Ibexa\Core\Base\Exceptions\NotFoundException;

final class AttributeTypeService implements AttributeTypeServiceInterface
{
    /** @var iterable<string,\Ibexa\Contracts\ProductCatalog\Values\AttributeTypeInterface> */
    private iterable $types;

    /**
     * @param iterable<string,\Ibexa\Contracts\ProductCatalog\Values\AttributeTypeInterface> $types
     */
    public function __construct(iterable $types)
    {
        $this->types = $types;
    }

    /**
     * @return iterable<string,\Ibexa\Contracts\ProductCatalog\Values\AttributeTypeInterface>
     */
    public function getAttributeTypes(): iterable
    {
        return $this->types;
    }

    public function getAttributeType(string $identifier): AttributeTypeInterface
    {
        $type = $this->findAttributeType($identifier);

        if ($type === null) {
            throw new NotFoundException(AttributeTypeInterface::class, $identifier);
        }

        return $type;
    }

    public function hasAttributeType(string $identifier): bool
    {
        return $this->findAttributeType($identifier) !== null;
    }

    private function findAttributeType(string $needle): ?AttributeTypeInterface
    {
        foreach ($this->types as $identifier => $type) {
            if ($identifier === $needle) {
                return $type;
            }
        }

        return null;
    }
}
