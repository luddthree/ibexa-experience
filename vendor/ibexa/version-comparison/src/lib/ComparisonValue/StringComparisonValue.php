<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\ComparisonValue;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

class StringComparisonValue extends ValueObject
{
    /** @var string */
    public const SPLIT_BY_SPACES = '~(?<=\s)~';

    /**
     * Positive Lookahead for '.' and '/' characters.
     *
     * @var string
     */
    public const SPLIT_BY_DOT_AND_SLASH = '~(?=[\.\/])~';

    /** @var string|null */
    public $value;

    /**
     * Regex for preg_split function used by StringComparisonEngine.
     *
     * @var string
     */
    public $splitBy = self::SPLIT_BY_SPACES;

    /** @var bool */
    public $doNotSplit = false;
}

class_alias(StringComparisonValue::class, 'EzSystems\EzPlatformVersionComparison\ComparisonValue\StringComparisonValue');
