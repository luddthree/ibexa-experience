<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Gateway;

use Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadataInterface as CoreDoctrineSchemaMetadataInterface;

/**
 * Contains metadata for converting values between database & PHP.
 *
 * Conceptually based on Doctrine's ClassMetadata, but also acts as type converter.
 *
 * @see \Doctrine\Persistence\Mapping\ClassMetadata
 */
interface DoctrineSchemaMetadataInterface extends CoreDoctrineSchemaMetadataInterface
{
}
