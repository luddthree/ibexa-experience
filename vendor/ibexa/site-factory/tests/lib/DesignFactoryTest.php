<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\SiteFactory;

use ArrayIterator;
use Ibexa\Contracts\SiteFactory\Values\Design\Template;
use Ibexa\Contracts\SiteFactory\Values\Design\TemplateConfiguration;
use Ibexa\SiteFactory\DesignFactory;
use Ibexa\SiteFactory\DesignRegistry;
use Ibexa\SiteFactory\Provider\SiteFactoryConfigurationProvider;
use PHPUnit\Framework\TestCase;

class DesignFactoryTest extends TestCase
{
    /** @var \Ibexa\SiteFactory\Provider\SiteFactoryConfigurationProvider|\PHPUnit\Framework\MockObject\MockObject */
    private $configurationProvider;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location */
    private $siteSkeletonLocation;

    protected function setUp(): void
    {
        $this->configurationProvider = $this->createMock(SiteFactoryConfigurationProvider::class);
        $this->configurationProvider
            ->method('getTemplatesConfiguration')
            ->willReturn(new ArrayIterator([
                TemplateConfiguration::fromTemplate(
                    'ibexa_conference',
                    'ibexa_conference_design',
                    [
                        'name' => 'Ibexa Conference',
                        'thumbnail' => '/ibexa_conference.png',
                        'siteaccess_group' => 'group_1',
                    ],
                    [],
                    $this->siteSkeletonLocation
                ),
                TemplateConfiguration::fromTemplate(
                    'ibexa_roadshow',
                    'ibexa_roadshow_design',
                    [
                        'name' => 'Ibexa Roadshow',
                        'thumbnail' => '/ibexa_roadshow.png',
                        'siteaccess_group' => 'group_2',
                    ],
                    []
                ),
            ]));
    }

    public function testGetDesignRegistry(): void
    {
        $designFactory = new DesignFactory($this->configurationProvider);

        $this->assertEquals(
            new DesignRegistry(
                [
                    new Template(
                        'ibexa_conference',
                        'group_1',
                        'Ibexa Conference',
                        '/ibexa_conference.png',
                        'ibexa_conference_design',
                        [],
                        $this->siteSkeletonLocation
                    ),
                    new Template(
                        'ibexa_roadshow',
                        'group_2',
                        'Ibexa Roadshow',
                        '/ibexa_roadshow.png',
                        'ibexa_roadshow_design',
                        []
                    ),
                ]
            ),
            $designFactory->getDesignRegistry()
        );
    }

    public function testGetDesignRegistryWithEmptyTemplates(): void
    {
        $configurationProvider = $this->createMock(SiteFactoryConfigurationProvider::class);
        $configurationProvider
            ->method('getTemplatesConfiguration')
            ->willReturn(new ArrayIterator([]));
        $designFactory = new DesignFactory($configurationProvider);
        $this->assertEquals(
            new DesignRegistry([]),
            $designFactory->getDesignRegistry()
        );
    }
}

class_alias(DesignFactoryTest::class, 'EzSystems\EzPlatformSiteFactory\Tests\DesignFactoryTest');
