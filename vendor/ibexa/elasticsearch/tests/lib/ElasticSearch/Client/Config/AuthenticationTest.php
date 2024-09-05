<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\ElasticSearch\Client\Config;

use Ibexa\Elasticsearch\ElasticSearch\Client\Config\Authentication;
use PHPUnit\Framework\TestCase;

final class AuthenticationTest extends TestCase
{
    /**
     * @dataProvider dataProviderForFromArray
     */
    public function testFromArray(array $properties, Authentication $expectedValue): void
    {
        $this->assertEquals($expectedValue, Authentication::fromArray($properties));
    }

    public function dataProviderForFromArray(): iterable
    {
        yield [
            [
                'type' => Authentication::TYPE_BASIC,
                'credentials' => ['username', 'password'],
            ],
            new Authentication(Authentication::TYPE_BASIC, ['username', 'password']),
        ];
    }
}

class_alias(AuthenticationTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\ElasticSearch\Client\Config\AuthenticationTest');
