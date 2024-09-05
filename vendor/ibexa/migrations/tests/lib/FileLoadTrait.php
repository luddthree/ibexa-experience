<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration;

use Webmozart\Assert\Assert;

trait FileLoadTrait
{
    final protected static function loadFile(string $filepath): string
    {
        $expected = file_get_contents($filepath);
        Assert::notFalse($expected, sprintf(
            'File "%s" is missing or not readable',
            $filepath,
        ));

        return $expected;
    }
}

class_alias(FileLoadTrait::class, 'Ibexa\Platform\Tests\Migration\FileLoadTrait');
