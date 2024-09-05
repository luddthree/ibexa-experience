<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository;

use Ibexa\Tests\Integration\ProductCatalog\IbexaKernelTestCase;

abstract class BaseAttributeGroupServiceTest extends IbexaKernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
        self::getLanguageResolver()->setContextLanguage('eng-US');
        self::setAdministratorUser();
    }
}
