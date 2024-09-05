<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Authentication;

final class Parameters
{
    private int $customerId;

    private string $licenseKey;

    public function __construct(int $customerId, string $licenseKey)
    {
        $this->customerId = $customerId;
        $this->licenseKey = $licenseKey;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function getLicenseKey(): string
    {
        return $this->licenseKey;
    }
}
