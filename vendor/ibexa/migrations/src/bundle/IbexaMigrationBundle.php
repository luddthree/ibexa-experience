<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration;

use Ibexa\Bundle\Migration\DependencyInjection\IbexaMigrationExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class IbexaMigrationBundle extends Bundle
{
    public function getContainerExtension(): IbexaMigrationExtension
    {
        return new IbexaMigrationExtension();
    }
}

class_alias(IbexaMigrationBundle::class, 'Ibexa\Platform\Bundle\Migration\IbexaPlatformMigrationBundle');
