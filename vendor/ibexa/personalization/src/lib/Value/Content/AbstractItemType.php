<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Content;

use Stringable;
use Symfony\Component\Serializer\Annotation\DiscriminatorMap;

/**
 * @DiscriminatorMap(typeProperty="type", mapping={
 *      "crossContentType"="Ibexa\Personalization\Value\Content\CrossContentType",
 *      "itemType"="Ibexa\Personalization\Value\Content\ItemType",
 * })
 */
abstract class AbstractItemType implements Stringable
{
    private string $description;

    public function __construct(string $description)
    {
        $this->description = $description;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function __toString(): string
    {
        return $this->getDescription();
    }
}
