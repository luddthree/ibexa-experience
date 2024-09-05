<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\User;

final class Matcher
{
    public const LOGIN = 'login';
    public const ID = 'id';
}

/**
 * @deprecated Use Ibexa\Migration\ValueObject\User\Matcher class instead
 */
class_alias(Matcher::class, 'Ibexa\Platform\Migration\ValueObject\User\Match');

class_alias(Matcher::class, 'Ibexa\Platform\Migration\ValueObject\User\Matcher');
