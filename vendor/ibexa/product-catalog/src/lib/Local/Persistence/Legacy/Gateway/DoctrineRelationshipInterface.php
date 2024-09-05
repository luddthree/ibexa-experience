<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Gateway;

interface DoctrineRelationshipInterface
{
    /**
     * @return class-string
     */
    public function getRelationshipClass(): string;

    /**
     * @return non-empty-string
     */
    public function getRelatedClassIdColumn(): string;

    /**
     * @return non-empty-string
     */
    public function getForeignProperty(): string;

    public function getForeignKeyColumn(): string;
}
