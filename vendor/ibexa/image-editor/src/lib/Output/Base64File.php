<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ImageEditor\Output;

final class Base64File
{
    /** @var string */
    public $base64;

    /** @var string */
    public $fileName;

    public function __construct(string $base64, string $fileName)
    {
        $this->base64 = $base64;
        $this->fileName = $fileName;
    }
}

class_alias(Base64File::class, 'Ibexa\Platform\ImageEditor\Output\Base64File');
