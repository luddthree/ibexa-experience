<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\User;

use Ibexa\Contracts\Core\Repository\Values\User\User;

final class CreateMetadata
{
    /** @var string */
    public $login;

    /** @var string */
    public $email;

    /** @var string */
    public $password;

    /** @var bool */
    public $enabled;

    /** @var string */
    public $mainLanguage;

    /** @var string|null */
    public $contentType;

    private function __construct(
        string $login,
        string $email,
        bool $enabled,
        string $password,
        string $mainLanguage,
        ?string $contentType
    ) {
        $this->login = $login;
        $this->email = $email;
        $this->enabled = $enabled;
        $this->password = $password;
        $this->mainLanguage = $mainLanguage;
        $this->contentType = $contentType;
    }

    public static function createFromApi(User $user, string $password): self
    {
        return new self(
            $user->login,
            $user->email,
            $user->enabled,
            $password,
            $user->contentInfo->mainLanguageCode,
            $user->getContentType()->identifier
        );
    }

    /**
     * @param array{
     *     login: string,
     *     email: string,
     *     enabled?: bool,
     *     password: string,
     *     mainLanguage: string,
     *     contentType?: ?string,
     * } $data
     */
    public static function createFromArray(array $data): self
    {
        return new self(
            $data['login'],
            $data['email'],
            $data['enabled'] ?? true,
            $data['password'],
            $data['mainLanguage'],
            $data['contentType'] ?? null,
        );
    }
}

class_alias(CreateMetadata::class, 'Ibexa\Platform\Migration\ValueObject\User\CreateMetadata');
