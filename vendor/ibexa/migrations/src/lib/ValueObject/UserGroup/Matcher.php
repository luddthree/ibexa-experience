<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\UserGroup;

use Ibexa\Migration\ValueObject\AbstractMatcher;

final class Matcher extends AbstractMatcher
{
    public const ID = 'id';
    public const REMOTE_ID = 'remoteId';

    private const SUPPORTED_FIELDS = [
        self::ID => self::ID,
        self::REMOTE_ID => self::REMOTE_ID,
    ];

    /**
     * @return array<string, string>
     */
    protected function getSupportedFields(): array
    {
        return self::SUPPORTED_FIELDS;
    }
}

/**
 * @deprecated Use Ibexa\Migration\ValueObject\UserGroup\Matcher class instead
 */
class_alias(Matcher::class, 'Ibexa\Platform\Migration\ValueObject\UserGroup\Match');

class_alias(Matcher::class, 'Ibexa\Platform\Migration\ValueObject\UserGroup\Matcher');
