<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Content\Metadata;

use InvalidArgumentException;

final class Section
{
    /** @var int|null */
    private $id;

    /** @var string|null */
    private $identifier;

    public function __construct(?int $id, ?string $identifier)
    {
        if ($id === null && $identifier === null) {
            throw new InvalidArgumentException('Either "id" or "identifier" argument must not be null');
        }

        $this->id = $id;
        $this->identifier = $identifier;
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

class_alias(Section::class, 'Ibexa\Platform\Migration\ValueObject\Content\Metadata\Section');
