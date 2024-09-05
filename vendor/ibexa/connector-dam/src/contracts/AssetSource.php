<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Connector\Dam;

final class AssetSource
{
    /** @var string */
    private $sourceIdentifier;

    public function __construct(string $sourceIdentifier)
    {
        $this->sourceIdentifier = $sourceIdentifier;
    }

    public function getSourceIdentifier(): string
    {
        return $this->sourceIdentifier;
    }
}

class_alias(AssetSource::class, 'Ibexa\Platform\Contracts\Connector\Dam\AssetSource');
