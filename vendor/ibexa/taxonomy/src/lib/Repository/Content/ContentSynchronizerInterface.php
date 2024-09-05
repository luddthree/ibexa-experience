<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Repository\Content;

use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;

/**
 * @internal
 */
interface ContentSynchronizerInterface
{
    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function synchronize(TaxonomyEntry $taxonomyEntry): void;

    public function reverseSynchronize(TaxonomyEntry $taxonomyEntry): void;
}
