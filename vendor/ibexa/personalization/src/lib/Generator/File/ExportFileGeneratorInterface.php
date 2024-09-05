<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Generator\File;

use Ibexa\Personalization\Value\Export\FileSettings;

/**
 * @internal
 */
interface ExportFileGeneratorInterface
{
    public function generate(FileSettings $fileSettings): void;
}
