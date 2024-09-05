<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Data;

final class UpdateInstallationKeyData
{
    /** @var string */
    private $installationKey;

    public function __construct(
        string $installationKey
    ) {
        $this->installationKey = $installationKey;
    }

    public function getInstallationKey(): string
    {
        return $this->installationKey;
    }

    public function setInstallationKey(string $installationKey): void
    {
        $this->installationKey = $installationKey;
    }
}

class_alias(UpdateInstallationKeyData::class, 'Ibexa\Platform\Personalization\Form\Data\UpdateInstallationKeyData');
