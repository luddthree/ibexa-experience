<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration;

use function dirname;

class FixtureFiles
{
    public static function getSchemaFileLocation(): string
    {
        return dirname(__DIR__, 2)
            . '/vendor/ezsystems/ezplatform-kernel/eZ/Bundle/EzPublishCoreBundle/Resources/config/storage/legacy/schema.yaml';
    }

    public static function getFixtureFileLocation(): string
    {
        return dirname(__DIR__, 2)
            . '/vendor/ezsystems/ezplatform-kernel/eZ/Publish/API/Repository/Tests/_fixtures/Legacy/data/test_data.yaml';
    }
}

class_alias(FixtureFiles::class, 'Ibexa\Platform\Tests\Bundle\Migration\FixtureFiles');
