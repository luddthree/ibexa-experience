<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\REST;

use Ibexa\Tests\Integration\ProductCatalog\BaseWebTestCase;

abstract class BaseRestWebTestCase extends BaseWebTestCase
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
        self::assertStringContainsString($resourceType, $response);
        self::assertResponseHeaderSame(
            'Content-Type',
            $this->generateMediaTypeString($resourceType . '+json')
        );

        $this->validateAgainstJSONSchema($response);
    }

    private function generateMediaTypeString(string $typeString): string
    {
        return 'application/vnd.ibexa.api.' . $typeString;
    }
}
