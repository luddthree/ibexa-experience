<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step\Action\UserGroup;

use Ibexa\Migration\ValueObject\Step\Action;

final class UnassignRole implements Action
{
    public const TYPE = 'unassign_role_user_group';

    /** @var int */
    private $id;

    public function getSupportedType(): string
    {
        return self::TYPE;
    }

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
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

class_alias(UnassignRole::class, 'Ibexa\Platform\Migration\ValueObject\Step\Action\UserGroup\UnassignRole');
