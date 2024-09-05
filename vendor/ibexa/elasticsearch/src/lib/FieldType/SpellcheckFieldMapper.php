<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\FieldType;

use Ibexa\Contracts\Core\Search\Field;
use Ibexa\Core\Search\Common\FieldValueMapper\StringMapper;

final class SpellcheckFieldMapper extends StringMapper
{
    public function canMap(Field $field): bool
    {
        return $field->getType() instanceof SpellcheckField;
    }
}
