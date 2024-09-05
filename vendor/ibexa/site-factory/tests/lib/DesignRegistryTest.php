<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\SiteFactory;

use Ibexa\Contracts\SiteFactory\Values\Design\Template;
use Ibexa\SiteFactory\DesignRegistry;
use PHPUnit\Framework\TestCase;

class DesignRegistryTest extends TestCase
{
    private const FOO_TEMPLATE = 'foo';
    private const BAR_TEMPLATE = 'bar';
    private const BAZ_TEMPLATE = 'baz';
    private const QUX_TEMPLATE = 'qux';

    /** @var \Ibexa\Contracts\SiteFactory\Values\Design\Template[] */
    private $templates;

    /** @var \Ibexa\Contracts\SiteFactory\Values\Design\Template */
    private $fooTemplate;

    /** @var \Ibexa\Contracts\SiteFactory\Values\Design\Template */
    private $barTemplate;

    /** @var \Ibexa\Contracts\SiteFactory\Values\Design\Template */
    private $bazTemplate;

    protected function setUp(): void
    {
        $this->fooTemplate = $this->createTemplate(self::FOO_TEMPLATE);
        $this->barTemplate = $this->createTemplate(self::BAR_TEMPLATE);
        $this->bazTemplate = $this->createTemplate(self::BAZ_TEMPLATE);

        $this->templates = [
            $this->fooTemplate,
            $this->barTemplate,
            $this->bazTemplate,
        ];
    }

    public function testGetTemplates(): void
    {
        $this->assertEquals(
            $this->templates,
            array_values((new DesignRegistry($this->templates))->getTemplates())
        );
    }

    public function testGetTemplateByDesignAndSiteAccessGroup(): void
    {
        $quxTemplateWithFooDesign = new Template(
            self::QUX_TEMPLATE,
            self::QUX_TEMPLATE . '_site_access_group',
            self::QUX_TEMPLATE . '_name',
            self::QUX_TEMPLATE . '_thumbnail',
            self::FOO_TEMPLATE . '_design',
            []
        );

        $this->templates[] = $quxTemplateWithFooDesign;
        $designRegistry = new DesignRegistry($this->templates);

        $this->assertEquals(
            $quxTemplateWithFooDesign,
            $designRegistry->getTemplate(
                self::FOO_TEMPLATE . '_design',
                self::QUX_TEMPLATE . '_site_access_group'
            )
        );
    }

    public function testGetMissingTemplateByDesignAndSiteAccessGroup(): void
    {
        $designRegistry = new DesignRegistry($this->templates);

        $this->assertNull(
            $designRegistry->getTemplate(
                self::FOO_TEMPLATE . '_design',
                'missingSiteAccessGroup'
            )
        );
    }

    public function testGetTemplateByIdentifier(): void
    {
        $designRegistry = new DesignRegistry($this->templates);

        $this->assertEquals(
            $this->fooTemplate,
            $designRegistry->getTemplateByIdentifier(self::FOO_TEMPLATE)
        );
    }

    public function testGetMissingTemplateByIdentifier(): void
    {
        $designRegistry = new DesignRegistry($this->templates);

        $this->assertNull(
            $designRegistry->getTemplateByIdentifier('missing')
        );
    }

    private function createTemplate(string $identifier): Template
    {
        return new Template(
            $identifier,
            $identifier . '_site_access_group',
            $identifier . '_name',
            $identifier . '_thumbnail',
            $identifier . '_design',
            []
        );
    }
}

class_alias(DesignRegistryTest::class, 'EzSystems\EzPlatformSiteFactory\Tests\DesignRegistryTest');
