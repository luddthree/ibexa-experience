<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Stubs\Model;

use Ibexa\Personalization\Value\Model\Attribute;

final class TestAttribute
{
    public const ATTRIBUTE_KEY = 'foo';
    public const ATTRIBUTE_TYPE = Attribute::TYPE_NOMINAL;
    public const ATTRIBUTE_SOURCE = 'USER';
    public const SOURCE = 'allSources';
}
