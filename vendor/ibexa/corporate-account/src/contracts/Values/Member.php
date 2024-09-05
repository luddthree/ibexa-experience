<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\Values;

use Ibexa\Contracts\Core\FieldType\Value;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\Content\Thumbnail;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;

final class Member extends ValueObject
{
    private User $user;

    private Company $company;

    private Role $role;

    public function __construct(User $user, Company $company, Role $role)
    {
        parent::__construct();
        $this->user = $user;
        $this->company = $company;
        $this->role = $role;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getVersionInfo(): VersionInfo
    {
        return $this->user->getVersionInfo();
    }

    public function getFieldValue(string $fieldDefIdentifier, ?string $languageCode = null): ?Value
    {
        return $this->user->getFieldValue($fieldDefIdentifier, $languageCode);
    }

    /** @return \Ibexa\Contracts\Core\Repository\Values\Content\Field[] */
    public function getFields(): iterable
    {
        return $this->user->getFields();
    }

    /** @return \Ibexa\Contracts\Core\Repository\Values\Content\Field[] */
    public function getFieldsByLanguage(?string $languageCode = null): iterable
    {
        return $this->user->getFieldsByLanguage($languageCode);
    }

    public function getField(string $fieldDefIdentifier, ?string $languageCode = null): ?Field
    {
        return $this->user->getField($fieldDefIdentifier, $languageCode);
    }

    public function getContentType(): ContentType
    {
        return $this->user->getContentType();
    }

    public function getThumbnail(): ?Thumbnail
    {
        return $this->user->getThumbnail();
    }

    public function getDefaultLanguageCode(): string
    {
        return $this->user->getDefaultLanguageCode();
    }

    public function getName(): ?string
    {
        return $this->user->getName();
    }

    public function getId(): int
    {
        return $this->user->getUserId();
    }
}
