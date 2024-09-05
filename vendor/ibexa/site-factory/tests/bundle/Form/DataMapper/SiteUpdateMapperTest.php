<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\SiteFactory\Form\DataMapper;

use Ibexa\Bundle\SiteFactory\Form\Data\PublicAccessUpdateData;
use Ibexa\Bundle\SiteFactory\Form\Data\SiteMatcherConfigurationData;
use Ibexa\Bundle\SiteFactory\Form\Data\SiteUpdateData;
use Ibexa\Bundle\SiteFactory\Form\DataMapper\SiteUpdateMapper;
use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\SiteFactory\Values\Design\Template;
use Ibexa\Contracts\SiteFactory\Values\Site\PublicAccess;
use Ibexa\Contracts\SiteFactory\Values\Site\Site;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteAccessMatcherConfiguration;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteConfiguration;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteUpdateStruct;
use PHPUnit\Framework\TestCase;

final class SiteUpdateMapperTest extends TestCase
{
    private const SITE_NAME = 'Site name';
    private const SITE_ID = 3;
    private const SA_GROUP = 'site_access_group';

    private const PUBLIC_ACCESS_IDENTIFIER = 'identifier';
    private const PUBLIC_ACCESS_LANGUAGES = ['pol_PL', 'ger_DE'];
    private const DESIGN_NAME = 'design_name';
    private const TREE_ROOT_LOCATION_ID = 33;
    private const HOST = 'host';

    private const TEMPLATE_IDENTIFIER = 'template_identifier';
    private const TEMPLATE_NAME = 'template_name';
    private const TEMPLATE_THUMBNAIL_PATH = 'template_thumbnail_path';

    /** @var \Ibexa\Bundle\SiteFactory\Form\DataMapper\SiteCreateMapper */
    private $mapper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mapper = new SiteUpdateMapper();
    }

    public function testReverseMap(): void
    {
        $publicAccess = new PublicAccess(
            self::PUBLIC_ACCESS_IDENTIFIER,
            self::SITE_ID,
            self::SA_GROUP,
            new SiteAccessMatcherConfiguration(),
            new SiteConfiguration([
                'ibexa.site_access.config.languages' => self::PUBLIC_ACCESS_LANGUAGES,
                'ibexa.site_access.config.design' => self::DESIGN_NAME,
                'ibexa.site_access.config.content.tree_root.location_id' => self::TREE_ROOT_LOCATION_ID,
            ]),
            PublicAccess::STATUS_OFFLINE
        );

        $template = new Template(
            self::TEMPLATE_IDENTIFIER,
            self::SA_GROUP,
            self::TEMPLATE_NAME,
            self::TEMPLATE_THUMBNAIL_PATH,
            self::DESIGN_NAME,
            []
        );

        $site = new Site([
            'id' => self::SITE_ID,
            'status' => Site::STATUS_ONLINE,
            'publicAccesses' => [$publicAccess],
            'name' => self::SITE_NAME,
            'created' => 12345678,
            'languages' => self::PUBLIC_ACCESS_LANGUAGES,
            'template' => $template,
        ]);
        $data = SiteUpdateData::fromSite($site);
        $result = $this->mapper->reverseMap($data);

        $expectedPublicAccess = new PublicAccess(
            self::PUBLIC_ACCESS_IDENTIFIER,
            null,
            self::SA_GROUP,
            new SiteAccessMatcherConfiguration(),
            new SiteConfiguration([
                'ibexa.site_access.config.languages' => self::PUBLIC_ACCESS_LANGUAGES,
                'ibexa.site_access.config.design' => self::DESIGN_NAME,
                'ibexa.site_access.config.content.tree_root.location_id' => self::TREE_ROOT_LOCATION_ID,
            ]),
            PublicAccess::STATUS_OFFLINE
        );
        $expected = new SiteUpdateStruct(self::SITE_NAME, [$expectedPublicAccess]);
        $this->assertEquals(
            $expected,
            $result
        );
    }

    public function testReverseMapWillThrowException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument \'data\' is invalid: must be an instance of ' . SiteUpdateData::class);

        $this->mapper->reverseMap(new \stdClass());
    }

    public function testMap(): void
    {
        $publicAccess = new PublicAccess(
            self::PUBLIC_ACCESS_IDENTIFIER,
            null,
            self::SA_GROUP,
            new SiteAccessMatcherConfiguration(self::HOST),
            new SiteConfiguration([
                'ibexa.site_access.config.languages' => self::PUBLIC_ACCESS_LANGUAGES,
                'ibexa.site_access.config.design' => self::DESIGN_NAME,
                'ibexa.site_access.config.content.tree_root.location_id' => self::TREE_ROOT_LOCATION_ID,
            ]),
            PublicAccess::STATUS_OFFLINE
        );

        $siteUpdateStruct = new SiteUpdateStruct(self::SITE_NAME, [$publicAccess]);
        $result = $this->mapper->map($siteUpdateStruct);

        $publicAccessUpdateData = new PublicAccessUpdateData();
        $publicAccessUpdateData->setStatus(PublicAccess::STATUS_OFFLINE);
        $siteMatcherConfigurationData = new SiteMatcherConfigurationData();
        $siteMatcherConfigurationData->setHost(self::HOST);
        $publicAccessUpdateData->setMatcherConfiguration($siteMatcherConfigurationData);
        $publicAccessUpdateData->setConfig(['languages' => self::PUBLIC_ACCESS_LANGUAGES]);

        $expectedSiteUpdateData = new SiteUpdateData([$publicAccessUpdateData]);
        $expectedSiteUpdateData->setSiteName(self::SITE_NAME);
        $expectedSiteUpdateData->setTreeRootLocationId(self::TREE_ROOT_LOCATION_ID);

        $this->assertEquals(
            $expectedSiteUpdateData,
            $result
        );
    }

    public function testMapWillThrowException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument \'value\' is invalid: must be an instance of ' . SiteUpdateStruct::class);

        $this->mapper->map(new class() extends ValueObject {
        });
    }
}

class_alias(SiteUpdateMapperTest::class, 'EzSystems\EzPlatformSiteFactoryBundle\Tests\Form\DataMapper\SiteUpdateMapperTest');
