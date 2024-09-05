<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Dumper;

use function sprintf;

final class MigrationFile
{
    /** @var string */
    private $filename;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public static function createWithGeneratedName(string $migrationType, string $dataFormat): self
    {
        $generatedName = self::generateFilename($migrationType, $dataFormat);

        return new self($generatedName);
    }

    private static function generateFilename(string $migrationType, string $dataFormat): string
    {
        $time = str_replace('.', '_', sprintf('%f', microtime(true)));

        return sprintf('%s_%s.%s', $time, $migrationType, $dataFormat);
    }
}

class_alias(MigrationFile::class, 'Ibexa\Platform\Migration\Dumper\MigrationFile');
