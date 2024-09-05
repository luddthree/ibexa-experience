<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\SiteFactory\Form\DataMapper;

use Ibexa\Bundle\SiteFactory\Form\Data\PublicAccessData;
use Ibexa\Bundle\SiteFactory\Form\Data\SiteCreateData;
use Ibexa\Bundle\SiteFactory\Form\Data\SiteMatcherConfigurationData;
use Ibexa\Bundle\SiteFactory\Form\DataMapper\SiteCreateMapper;
use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\SiteFactory\Values\Design\Template;
use Ibexa\Contracts\SiteFactory\Values\Site\PublicAccess;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteAccessMatcherConfiguration;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteConfiguration;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteCreateStruct;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\ClockMock;

final class SiteCreateMapperTest extends TestCase
{
    private const SITE_NAME = 'Site name';
    private const SITE_ID = 3;
    private const SA_GROUP = 'site_access_group';
    private const PARENT_LOCATION_ID = 2;

    private const PUBLIC_ACCESS_IDENTIFIER = 'identifier';
    private const PUBLIC_ACCESS_LANGUAGES = ['pol_PL', 'ger_DE'];
    private const DESIGN_NAME = 'design_name';
    private const TREE_ROOT_LOCATION_ID = 33;
    private const HOST = 'host';

    private const TEMPLATE_IDENTIFIER = 'template_identifier';
    private const TEMPLATE_NAME = 'template_name';
    private const TEMPLATE_THUMBNAIL_PATH = 'template_thumbnail_path';
    private const SITE_SKELETON_LOCATION_ID = 42;
    private const USER_GROUP_SKELETON_LOCATION_ID = 50;

    /** @var \Ibexa\Bundle\SiteFactory\Form\DataMapper\SiteCreateMapper */
    private $mapper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mapper = new SiteCreateMapper();
    }

    public function testReverseMap(): void
    {
        ClockMock::register(SiteCreateMapper::class);
        ClockMock::withClockMock(true);
        $publicAccessData = new PublicAccessData();
        $publicAccessData->setConfig([
            'languages' => self::PUBLIC_ACCESS_LANGUAGES,
        ]);
        $data = new SiteCreateData([$publicAccessData]);
        $data->setTreeRootLocationId(self::TREE_ROOT_LOCATION_ID);
        $data->setDesign(new Template(
            self::TEMPLATE_IDENTIFIER,
            self::SA_GROUP,
            self::TEMPLATE_NAME,
            self::TEMPLATE_THUMBNAIL_PATH,
            self::DESIGN_NAME,
            [
                $this->getSkeletonLocation([
                    'id' => self::USER_GROUP_SKELETON_LOCATION_ID,
                ]),
            ],
            $this->getSkeletonLocation([
                'id' => self::SITE_SKELETON_LOCATION_ID,
            ]),
        ));
        $data->setSiteName(self::SITE_NAME);
        $data->setParentLocationId(self::PARENT_LOCATION_ID);

        $result = $this->mapper->reverseMap($data);
        $publicAccess = new PublicAccess(
            $result->publicAccesses[0]->identifier,
            null,
            self::SA_GROUP,
            new SiteAccessMatcherConfiguration(),
            new SiteConfiguration([
                'ibexa.site_access.config.languages' => self::PUBLIC_ACCESS_LANGUAGES,
                'ibexa.site_access.config.design' => self::DESIGN_NAME,
            ])
        );

        $expected = new SiteCreateStruct(
            self::SITE_NAME,
            true,
            [$publicAccess],
            self::PARENT_LOCATION_ID,
            [self::USER_GROUP_SKELETON_LOCATION_ID],
            null,
            self::SITE_SKELETON_LOCATION_ID
        );

        $this->assertEquals(
            $expected,
            $result
        );
        ClockMock::withClockMock(false);
    }

    public function testReverseMapWillThrowException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument \'data\' is invalid: must be an instance of ' . SiteCreateData::class);

        $this->mapper->reverseMap(new \stdClass());
    }

    public function testMap(): void
    {
        $publicAccess = new PublicAccess(
            self::PUBLIC_ACCESS_IDENTIFIER,
            self::SITE_ID,
            self::SA_GROUP,
            new SiteAccessMatcherConfiguration(self::HOST),
            new SiteConfiguration([
                'ibexa.site_access.config.languages' => self::PUBLIC_ACCESS_LANGUAGES,
                'ibexa.site_access.config.design' => self::DESIGN_NAME,
                'ibexa.site_access.config.content.tree_root.location_id' => self::TREE_ROOT_LOCATION_ID,
            ]),
            PublicAccess::STATUS_ONLINE
        );
        $publicAccesses = [
            $publicAccess,
        ];
        $siteCreateStruct = new SiteCreateStruct(
            self::SITE_NAME,
            false,
            $publicAccesses,
            self::PARENT_LOCATION_ID,
            [self::USER_GROUP_SKELETON_LOCATION_ID]
        );

        $result = $this->mapper->map($siteCreateStruct);

        $publicAccessCreateData = new PublicAccessData();
        $publicAccessCreateData->setStatus(PublicAccess::STATUS_ONLINE);
        $siteMatcherConfigurationData = new SiteMatcherConfigurationData();
        $siteMatcherConfigurationData->setHost(self::HOST);
        $publicAccessCreateData->setMatcherConfiguration($siteMatcherConfigurationData);
        $publicAccessCreateData->setConfig(['languages' => self::PUBLIC_ACCESS_LANGUAGES]);

        $expectedSiteCreateData = new SiteCreateData([$publicAccessCreateData]);
        $expectedSiteCreateData->setSiteName(self::SITE_NAME);
        $expectedSiteCreateData->setTreeRootLocationId(self::TREE_ROOT_LOCATION_ID);

        $this->assertEquals($expectedSiteCreateData, $result);
    }

    public function testMapWillThrowException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument \'value\' is invalid: must be an instance of ' . SiteCreateStruct::class);

        $this->mapper->map(new class() extends ValueObject {
        });
    }

    private function getSkeletonLocation(array $constructorArguments): Location
    {
        return new class($constructorArguments) extends Location {
            public function getContentInfo(): ContentInfo
            {
                return new ContentInfo();
            }

            public function getParentLocation(): ?Location
            {
                return null;
            }
        };
    }
}

class_alias(SiteCreateMapperTest::class, 'EzSystems\EzPlatformSiteFactoryBundle\Tests\Form\DataMapper\SiteCreateMapperTest');
