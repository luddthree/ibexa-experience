<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Section;

use Ibexa\Migration\ValueObject\AbstractMatcher;

final class Matcher extends AbstractMatcher
{
    public const IDENTIFIER = 'identifier';
    public const ID = 'id';

    protected function getSupportedFields(): array
    {
        return [
            self::IDENTIFIER => self::IDENTIFIER,
            self::ID => self::ID,
        ];
    }
}

/**
 * @deprecated Use Ibexa\Migration\ValueObject\Section\Matcher class instead
 */
class_alias(Matcher::class, 'Ibexa\Platform\Migration\ValueObject\Section\Match');

class_alias(Matcher::class, 'Ibexa\Platform\Migration\ValueObject\Section\Matcher');
