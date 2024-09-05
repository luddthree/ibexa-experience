<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step\Action;

use Ibexa\Migration\ValueObject\Step\Action;
use InvalidArgumentException;

abstract class AbstractUserAssignRole implements Action, Action\RoleLimitationAwareInterface
{
    use Action\RoleLimitationAwareActionTrait;

    /** @var int|null */
    private $id;

    /** @var string|null */
    private $identifier;

    final public function __construct(?int $id = null, ?string $identifier = null)
    {
        $this->id = $id;
        $this->identifier = $identifier;

        if ($this->id === null && $this->identifier === null) {
            throw new InvalidArgumentException('Either "id" or "identifier" argument must not be null');
        }
    }

    abstract public function getSupportedType(): string;

    final public function getId(): ?int
    {
        return $this->id;
    }

    final public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    /**
     * @return array{
     *     'id': int,
     * }
     */
    public function getValue(): array
    {
        assert($this->id !== null);

        return [
            'id' => $this->id,
        ];
    }
}

class_alias(AbstractUserAssignRole::class, 'Ibexa\Platform\Migration\ValueObject\Step\Action\AbstractUserAssignRole');
