<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value;

final class Account
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

    /**
     * @param array{
     *     'mandatorId': string,
     *     'licenseKey': string,
     * } $properties
     */
    public static function fromArray(array $properties): self
    {
        return new self(
            (int) $properties['mandatorId'],
            $properties['licenseKey'],
        );
    }
}
