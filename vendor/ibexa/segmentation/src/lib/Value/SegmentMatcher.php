<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Value;

use InvalidArgumentException;

final class SegmentMatcher
{
    /** @var int|null */
    private $id;

    /** @var string|null */
    private $identifier;

    public function __construct(?int $id = null, ?string $identifier = null)
    {
        $this->id = $id;
        $this->identifier = $identifier;

        if ($this->id === null && $this->identifier === null) {
            throw new InvalidArgumentException('"id" or "identifier" argument must not be null');
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }
}
