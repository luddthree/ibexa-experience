<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\FieldType\Author;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

final class Author extends ValueObject
{
    /** @var int */
    public $id;

    /** @var \Ibexa\VersionComparison\ComparisonValue\StringComparisonValue */
    public $name;

    /** @var \Ibexa\VersionComparison\ComparisonValue\StringComparisonValue */
    public $email;
}

class_alias(Author::class, 'EzSystems\EzPlatformVersionComparison\FieldType\Author\Author');
