<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration;

use Ibexa\Contracts\Core\Test\IbexaKernelTestCase as BaseIbexaKernelTestCase;
use Ibexa\Migration\Tests\MigrationTestServicesTrait;

abstract class IbexaKernelTestCase extends BaseIbexaKernelTestCase
{
    use MigrationTestAssertionsTrait;

    use MigrationTestServicesTrait;
}

class_alias(IbexaKernelTestCase::class, 'Ibexa\Platform\Tests\Bundle\Migration\IbexaKernelTestCase');
