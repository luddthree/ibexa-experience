<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Dashboard\Migrations;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Zone;
use Ibexa\Contracts\Test\Core\IbexaKernelTestCase;
use Ibexa\FieldTypePage\FieldType\LandingPage\Value as LandingPageValue;

final class DashboardMigrationTest extends IbexaKernelTestCase
{
    private const DEFAULT_DASHBOARD_CONTENT_REMOTE_ID = 'default_dashboard_landing_page';
    private const DEFAULT_DASHBOARD_SECTION_IDENTIFIER = 'dashboard';
    private const DEFAULT_DASHBOARD_CONTENT_TYPE_IDENTIFIER = 'dashboard_landing_page';
    private const DEFAULT_DASHBOARD_LOCATION_REMOTE_ID = 'default_dashboard';
    private const DEFAULT_DASHBOARD_PREDEFINED_LOCATION_REMOTE_ID = 'predefined_dashboards';
    private const DEFAULT_DASHBOARD_CONTAINER_LOCATION_REMOTE_ID = 'dashboard_container';

    private ContentService $contentService;

    protected function setUp(): void
    {
        parent::setUp();

        $ibexaTestCore = $this->getIbexaTestCore();
        $ibexaTestCore->setAdministratorUser();

        $this->contentService = $ibexaTestCore->getContentService();
    }

    /**
     * This test checks if the dashboard migration was properly executed when bootstrapping test framework.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     */
    public function testDefaultDashboardExists(): LandingPageValue
    {
        $content = $this->contentService->loadContentByRemoteId(self::DEFAULT_DASHBOARD_CONTENT_REMOTE_ID);

        $contentInfo = $content->getVersionInfo()->getContentInfo();
        $mainLocation = $contentInfo->getMainLocation();
        self::assertNotNull($mainLocation);

        $parentLocation = $mainLocation->getParentLocation();
        self::assertNotNull($parentLocation);

        self::assertSame(self::DEFAULT_DASHBOARD_SECTION_IDENTIFIER, $contentInfo->getSection()->identifier);
        self::assertSame(self::DEFAULT_DASHBOARD_CONTENT_TYPE_IDENTIFIER, $content->getContentType()->identifier);
        self::assertSame(self::DEFAULT_DASHBOARD_LOCATION_REMOTE_ID, $mainLocation->remoteId);
        self::assertSame(self::DEFAULT_DASHBOARD_PREDEFINED_LOCATION_REMOTE_ID, $parentLocation->remoteId);

        $predefinedDashboardsParentLocation = $parentLocation->getParentLocation();

        self::assertNotNull($predefinedDashboardsParentLocation);

        self::assertSame(
            self::DEFAULT_DASHBOARD_CONTAINER_LOCATION_REMOTE_ID,
            $predefinedDashboardsParentLocation->remoteId
        );

        $dashboardField = $content->getField('dashboard');
        self::assertNotNull($dashboardField, '"dashboard" field does not exist in dashboard content item');

        $landingPageValue = $dashboardField->getValue();
        self::assertInstanceOf(LandingPageValue::class, $landingPageValue);

        return $landingPageValue;
    }

    /**
     * @depends testDefaultDashboardExists
     */
    public function testDefaultMigratedDashboardContainsDefaultBlocks(LandingPageValue $landingPageValue): void
    {
        $page = $landingPageValue->getPage();
        // make zones key-value map for test expectation readability
        $zones = $this->mapZones($page);

        self::assertSame('dashboard_three_rows_two_columns', $page->getLayout());
        self::assertCount(4, $zones);

        self::assertSame(['top', 'middle-left', 'middle-right', 'bottom'], array_keys($zones));

        self::assertZoneHasBlocks($zones['top'], ['quick_actions']);
        self::assertZoneHasBlocks($zones['middle-left'], []); // activity log
        self::assertZoneHasBlocks($zones['middle-right'], ['ibexa_news']);
        self::assertZoneHasBlocks($zones['bottom'], ['review_queue', 'my_content', 'common_content']);
    }

    /**
     * @return array<string, \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Zone>
     */
    private function mapZones(Page $page): array
    {
        $zones = [];
        foreach ($page->getZones() as $zone) {
            $zones[$zone->getName()] = $zone;
        }

        return $zones;
    }

    /**
     * @param array<string> $expectedBlockTypes
     */
    private static function assertZoneHasBlocks(Zone $zone, array $expectedBlockTypes): void
    {
        $blockValues = $zone->getBlocks();
        self::assertCount(count($expectedBlockTypes), $blockValues);
        self::assertSame(
            $expectedBlockTypes,
            array_map(static fn (BlockValue $value): string => $value->getType(), $blockValues)
        );
    }
}
