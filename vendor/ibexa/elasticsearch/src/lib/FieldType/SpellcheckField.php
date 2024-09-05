<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\FieldType;

use Ibexa\Contracts\Core\Search\FieldType;

final class SpellcheckField extends FieldType
{
    protected $type = 'spellcheck';
}
