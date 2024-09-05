<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Data;

use Symfony\Component\Validator\Constraints as Assert;

final class AcceptanceData
{
    /** @var string */
    private $username;

    /** @var string */
    private $email;

    /**
     * @Assert\Length(
     *     max = 40
     * )
     *
     * @var string
     */
    private $installationKey;

    /** @var bool */
    private $termsAndConditions;

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getInstallationKey(): string
    {
        return $this->installationKey;
    }

    public function setInstallationKey(string $installationKey): void
    {
        $this->installationKey = $installationKey;
    }

    public function isTermsAndConditions(): bool
    {
        return $this->termsAndConditions;
    }

    public function setTermsAndConditions(bool $termsAndConditions): void
    {
        $this->termsAndConditions = $termsAndConditions;
    }
}

class_alias(AcceptanceData::class, 'Ibexa\Platform\Personalization\Form\Data\AcceptanceData');
