<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step\Action\Content;

use Ibexa\Migration\ValueObject\Step\Action;

final class AssignSection implements Action
{
    public const TYPE = 'assign_section';

    /** @var int|null */
    private $id;

    /** @var string|null */
    private $identifier;

    public function __construct(?int $id, ?string $identifier)
    {
        $this->id = $id;
        $this->identifier = $identifier;
    }

    public function getValue(): void
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function getSupportedType(): string
    {
        return self::TYPE;
    }
}
