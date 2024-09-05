<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Fixtures;

use Ibexa\Contracts\Core\Test\Persistence\Fixture;

final class CustomerGroupFixture implements Fixture
{
    public const FIXTURE_ENTRY_ID = 42;
    public const FIXTURE_ENTRY_IDENTIFIER = 'answer to everything';
    public const FIXTURE_SECOND_ENTRY_ID = 2;

    /**
     * @return array<string, array<array<string, scalar>>>
     */
    public function load(): array
    {
        return [
            'ibexa_customer_group' => [
                [
                    'id' => self::FIXTURE_ENTRY_ID,
                    'identifier' => self::FIXTURE_ENTRY_IDENTIFIER,
                    'global_price_rate' => '66.6',
                ],
                [
                    'id' => self::FIXTURE_SECOND_ENTRY_ID,
                    'identifier' => 'second customer group',
                    'global_price_rate' => '0',
                ],
                [
                    'id' => 3,
                    'identifier' => 'test-update',
                    'global_price_rate' => '0',
                ],
            ],
            'ibexa_customer_group_ml' => [
                [
                    'customer_group_id' => self::FIXTURE_ENTRY_ID,
                    'language_id' => 2,
                    'name' => 'Answer To Life, Universe and Everything',
                    'name_normalized' => 'answer to life, universe and everything',
                    'description' => '',
                ],
                [
                    'customer_group_id' => self::FIXTURE_SECOND_ENTRY_ID,
                    'language_id' => 2,
                    'name' => 'Second Customer Group',
                    'name_normalized' => 'second customer group',
                    'description' => 'Description for the second group',
                ],
                [
                    'customer_group_id' => 3,
                    'language_id' => 2,
                    'name' => 'foo',
                    'name_normalized' => 'foo',
                    'description' => 'Customer Group for testing update',
                ],
            ],
        ];
    }
}
