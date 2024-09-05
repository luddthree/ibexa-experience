<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\CorporateAccount\REST;

use Ibexa\Contracts\Test\Rest\BaseRestWebTestCase;

abstract class BaseCorporateAccountRestWebTestCase extends BaseRestWebTestCase
{
    protected function getSchemaFileBasePath(string $resourceType, string $format): string
    {
        return __DIR__ . '/../Resources/REST/schemas/' . $resourceType;
    }
}
