<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Fixtures;

use Ibexa\Contracts\Core\Test\Persistence\Fixture;

final class ContentCustomerGroupFixture implements Fixture
{
    public const FIXTURE_FIELD_ID = 28;
    public const FIXTURE_VERSION_NO = 4;
    public const FIXTURE_CONTENT_ID = 14;
    public const FIXTURE_CUSTOMER_GROUP_ID = CustomerGroupFixture::FIXTURE_ENTRY_ID;

    /**
     * @return array<string, array<array<string, scalar>>>
     */
    public function load(): array
    {
        return [
            'ibexa_content_customer_group' => [
                [
                    'customer_group_id' => self::FIXTURE_CUSTOMER_GROUP_ID,
                    'content_id' => self::FIXTURE_CONTENT_ID,
                    'field_id' => self::FIXTURE_FIELD_ID,
                    'field_version_no' => self::FIXTURE_VERSION_NO,
                ],
            ],
        ];
    }
}
