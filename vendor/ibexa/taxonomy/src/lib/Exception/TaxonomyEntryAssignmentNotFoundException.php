<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Exception;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;

final class TaxonomyEntryAssignmentNotFoundException extends NotFoundException
{
    public static function createWithId(int $id): self
    {
        return new self(
            sprintf("Taxonomy entry assignment with ID: '%d' not found.", $id)
        );
    }

    public static function createWithEntryAndContentId(int $entryId, int $contentId, int $versionNo): self
    {
        return new self(
            sprintf(
                "Taxonomy entry assignment for Entry ID: '%d', Content ID: '%d', Version Number: '%d' not found.",
                $entryId,
                $contentId,
                $versionNo,
            )
        );
    }
}
