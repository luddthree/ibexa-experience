<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Dumper;

interface MigrationDumperInterface
{
    public function dump(iterable $data, MigrationFile $migrationFile): void;
}

class_alias(MigrationDumperInterface::class, 'Ibexa\Platform\Migration\Dumper\MigrationDumperInterface');
