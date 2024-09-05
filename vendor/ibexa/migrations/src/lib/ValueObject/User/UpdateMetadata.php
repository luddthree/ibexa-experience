<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\User;

use Ibexa\Core\FieldType\User\Value;

final class UpdateMetadata
{
    /** @var string|null */
    public $email;

    /** @var string|null */
    public $password;

    /** @var bool|null */
    public $enabled;

    private function __construct(?string $email, ?bool $enabled, ?string $password)
    {
        $this->email = $email;
        $this->enabled = $enabled;
        $this->password = $password;
    }

    public static function createFromApi(Value $userAccount, ?string $password = null): self
    {
        return new self(
            $userAccount->email,
            $userAccount->enabled,
            $password
        );
    }

    /**
     * @param array{
     *     email?: ?string,
     *     enabled?: ?bool,
     *     password?: ?string,
     * } $data
     */
    public static function createFromArray(array $data): self
    {
        return new self(
            $data['email'] ?? null,
            $data['enabled'] ?? null,
            $data['password'] ?? null,
        );
    }
}

class_alias(UpdateMetadata::class, 'Ibexa\Platform\Migration\ValueObject\User\UpdateMetadata');
