<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Measurement\Value\Definition;

final class Sign
{
    public const SIGN_NONE = 'none';
    public const SIGN_GT = 'gt';
    public const SIGN_LT = 'lt';
    public const SIGN_GTE = 'gte';
    public const SIGN_LTE = 'lte';
    public const SIGN_PM = 'pm';

    private function __construct()
    {
        // This class is not supposed to be instantiated.
    }

    /**
     * @phpstan-return array<self::SIGN_*>
     */
    public static function getAllowedValues(): array
    {
        return [
            self::SIGN_NONE,
            self::SIGN_GT,
            self::SIGN_GTE,
            self::SIGN_LT,
            self::SIGN_LTE,
            self::SIGN_PM,
        ];
    }

    public static function isValidSign(string $sign): bool
    {
        return in_array($sign, self::getAllowedValues(), true);
    }
}
