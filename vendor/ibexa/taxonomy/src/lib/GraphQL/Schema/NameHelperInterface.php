<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\GraphQL\Schema;

/**
 * @internal
 */
interface NameHelperInterface
{
    public function getTaxonomyName(string $taxonomy): string;

    public function getTaxonomyTypeName(string $taxonomy): string;
}
