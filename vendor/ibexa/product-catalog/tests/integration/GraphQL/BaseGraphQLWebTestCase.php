<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\GraphQL;

use Ibexa\Tests\Integration\ProductCatalog\BaseWebTestCase;

abstract class BaseGraphQLWebTestCase extends BaseWebTestCase
{
    /**
     * @throws \JsonException
     */
    protected function assertResponseIsValid(string $response): void
    {
        $resourceType = $this->getResourceType();
        if ($resourceType === null) {
            return;
        }
        self::assertStringContainsString('data', $response);

        $this->validateAgainstJSONSchema($response);
    }
}
