<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Limitation;

use Ibexa\Contracts\Core\Repository\Values\User\Limitation;

final class PersonalizationAccessLimitation extends Limitation
{
    public const IDENTIFIER = 'PersonalizationAccess';

    public function getIdentifier(): string
    {
        return self::IDENTIFIER;
    }
}

class_alias(PersonalizationAccessLimitation::class, 'Ibexa\Platform\Personalization\Value\Limitation\PersonalizationAccessLimitation');
