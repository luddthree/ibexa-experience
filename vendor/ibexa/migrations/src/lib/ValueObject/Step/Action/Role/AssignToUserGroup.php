<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step\Action\Role;

use Ibexa\Migration\ValueObject\Step\Action;
use InvalidArgumentException;

final class AssignToUserGroup implements Action, Action\RoleLimitationAwareInterface
{
    use Action\RoleLimitationAwareActionTrait;

    public const TYPE = 'assign_role_to_user_group';

    /** @var int|null */
    private $id;

    /** @var string|null */
    private $remoteId;

    public function __construct(?int $id = null, ?string $remoteId = null)
    {
        $this->id = $id;
        $this->remoteId = $remoteId;

        if ($this->id === null && $this->remoteId === null) {
            throw new InvalidArgumentException('Either "id" or "remoteId" argument must not be null');
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRemoteId(): ?string
    {
        return $this->remoteId;
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

    public function getSupportedType(): string
    {
        return self::TYPE;
    }
}

class_alias(AssignToUserGroup::class, 'Ibexa\Platform\Migration\ValueObject\Step\Action\Role\AssignToUserGroup');
