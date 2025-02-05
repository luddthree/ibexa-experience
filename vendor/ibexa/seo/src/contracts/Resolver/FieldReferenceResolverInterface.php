<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Seo\Resolver;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;

interface FieldReferenceResolverInterface
{
    public function resolve(Content $content, string $fieldValue): string;
}
