<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\GraphQL\Schema;

use Ibexa\GraphQL\Schema\Domain\BaseNameHelper;

/**
 * @internal
 */
final class NameHelper extends BaseNameHelper implements NameHelperInterface
{
    public function getTaxonomyName(string $taxonomy): string
    {
        return lcfirst($this->toCamelCase($taxonomy) . 'Taxonomy');
    }

    public function getTaxonomyTypeName(string $taxonomy): string
    {
        return 'Taxonomy' . ucfirst($taxonomy);
    }
}
