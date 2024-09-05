<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ImageEditor\File;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @internal
 */
interface Base64FileTransformer
{
    public function transform(string $base64, ?string $fileName): UploadedFile;
}

class_alias(Base64FileTransformer::class, 'Ibexa\Platform\ImageEditor\File\Base64FileTransformer');
