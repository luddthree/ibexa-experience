<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Permission;

/**
 * @internal
 */
interface CustomerTypeCheckerInterface
{
    public function isCommerce(int $customerId): bool;

    public function isPublisher(int $customerId): bool;
}

class_alias(CustomerTypeCheckerInterface::class, 'Ibexa\Platform\Personalization\Permission\CustomerTypeCheckerInterface');
