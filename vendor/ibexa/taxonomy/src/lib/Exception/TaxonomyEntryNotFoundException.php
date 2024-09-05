<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Exception;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;

final class TaxonomyEntryNotFoundException extends NotFoundException
{
    public static function createForRoot(string $taxonomy): self
    {
        return new self(
            sprintf("Taxonomy root Entry for '%s' taxonomy not found.", $taxonomy)
        );
    }

    public static function createWithId(int $id): self
    {
        return new self(
            sprintf("Taxonomy Entry with ID: '%d' not found.", $id)
        );
    }

    public static function createWithIdentifier(string $identifier, string $taxonomyName): self
    {
        return new self(
            sprintf(
                "Taxonomy Entry with Identifier: '%s' not found in '%s' taxonomy.",
                $identifier,
                $taxonomyName
            )
        );
    }

    public static function createWithContentId(int $contentId): self
    {
        return new self(
            sprintf("Taxonomy Entry with Content ID: '%d' not found.", $contentId)
        );
    }
}
