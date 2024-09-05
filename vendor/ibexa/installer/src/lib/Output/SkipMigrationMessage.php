<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Installer\Output;

use Ibexa\Migration\Repository\Migration;

final class SkipMigrationMessage
{
    public static function createMessage(Migration $migration): string
    {
        return sprintf('"%s" is marked as already executed. Skipping.', $migration->getName());
    }
}

class_alias(SkipMigrationMessage::class, 'Ibexa\Platform\Installer\Output\SkipMigrationMessage');
