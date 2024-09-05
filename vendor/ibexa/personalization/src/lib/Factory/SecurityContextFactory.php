<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Factory;

use Ibexa\Personalization\Value\Security\PersonalizationSecurityContext;

interface SecurityContextFactory
{
    public function createSecurityContextObject(int $customerId): PersonalizationSecurityContext;
}

class_alias(SecurityContextFactory::class, 'Ibexa\Platform\Personalization\Factory\SecurityContextFactory');
