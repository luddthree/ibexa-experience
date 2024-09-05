<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Event;

final class MigrationEvents
{
    public const AFTER_HASH_FROM_FIELD_VALUE = 'ibexa.migrations.hash_from_field_value.after';

    public const BEFORE_FIELD_VALUE_FROM_HASH = 'ibexa.migrations.field_value_from_hash.before';
}

class_alias(MigrationEvents::class, 'Ibexa\Platform\Migration\Event\MigrationEvents');
