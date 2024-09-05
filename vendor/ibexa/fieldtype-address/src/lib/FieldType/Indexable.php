<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypeAddress\FieldType;

use Ibexa\Contracts\Core\FieldType\Indexable as IndexableInterface;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition;

final class Indexable implements IndexableInterface
{
    public function getIndexData(Field $field, FieldDefinition $fieldDefinition): array
    {
        return [];
    }

    public function getIndexDefinition(): array
    {
        return [];
    }

    public function getDefaultMatchField(): ?string
    {
        return null;
    }

    public function getDefaultSortField(): ?string
    {
        return null;
    }
}
