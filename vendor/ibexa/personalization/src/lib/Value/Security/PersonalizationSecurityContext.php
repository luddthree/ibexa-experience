<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Security;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

final class PersonalizationSecurityContext extends ValueObject
{
    /** @var int */
    public $customerId;
}

class_alias(PersonalizationSecurityContext::class, 'Ibexa\Platform\Personalization\Value\Security\PersonalizationSecurityContext');
