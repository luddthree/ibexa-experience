<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\PageBuilder\Persistence\Legacy\MigrateRichTextNamespaces\Gateway;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Attribute;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Zone;
use Ibexa\Contracts\FieldTypeRichText\Persistence\Legacy\MigrateRichTextNamespaces\GatewayInterface;
use Ibexa\FieldTypePage\FieldType\LandingPage\Value;
use Ibexa\PageBuilder\Persistence\Legacy\MigrateRichTextNamespaces\Gateway\DoctrineDatabase;
use Ibexa\Tests\Integration\PageBuilder\BasePageBuilderIntegrationTestCase;

/**
 * @covers \Ibexa\PageBuilder\Persistence\Legacy\MigrateRichTextNamespaces\Gateway\DoctrineDatabase
 */
final class DoctrineDatabaseTest extends BasePageBuilderIntegrationTestCase
{
    private GatewayInterface $gateway;

    /** @var array<string, string> */
    private array $xmlNamespacesMigrationMap;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gateway = self::getServiceByClassName(DoctrineDatabase::class);
        $this->xmlNamespacesMigrationMap = $this->getXmlNamespacesMigrationMapParameter();
        self::setAdministratorUser();
    }

    /**
     * @dataProvider provideDataForTestMigrate
     */
    public function testMigrate(string $expected, string $xmlToMigrate): void
    {
        $page = $this->createLandingPageContent($xmlToMigrate);

        self::assertPageZones([$this->createZoneWithRichTextBlock($xmlToMigrate)], $page);

        self::assertGreaterThan(
            0,
            $this->gateway->migrate($this->xmlNamespacesMigrationMap)
        );

        $this->invalidateContentItemPersistenceCache(
            $page->id,
            $page->getVersionInfo()->versionNo,
            $page->getDefaultLanguageCode()
        );

        $page = $this->contentService->loadContent($page->id);

        self::assertPageZones([$this->createZoneWithRichTextBlock($expected)], $page);
    }

    /**
     * @return iterable<array{string, string}>
     */
    public function provideDataForTestMigrate(): iterable
    {
        yield [
            <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<section xmlns="http://docbook.org/ns/docbook" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:a="http://ibexa.co/xmlns/annotation" xmlns:m="http://ibexa.co/xmlns/module" xmlns:ez="http://ibexa.co/xmlns/dxp/docbook" xmlns:ezxhtml="http://ibexa.co/xmlns/dxp/docbook/xhtml" xmlns:ezcustom="http://ibexa.co/xmlns/dxp/docbook/custom" version="5.0-variant ezpublish-1.0">
    <para>RichText namespace migration test</para>
    <ezembed xlink:href="ezcontent://172" view="embed" ezxhtml:class="ibexa-embed-type-image"><ezconfig><ezvalue key="size">medium</ezvalue></ezconfig></ezembed>
</section>
XML,
            <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<section xmlns="http://docbook.org/ns/docbook" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:a="http://ez.no/xmlns/annotation" xmlns:m="http://ez.no/xmlns/module" xmlns:ez="http://ez.no/xmlns/ezpublish/docbook" xmlns:ezxhtml="http://ez.no/xmlns/ezpublish/docbook/xhtml" xmlns:ezcustom="http://ez.no/xmlns/ezpublish/docbook/custom" version="5.0-variant ezpublish-1.0">
    <para>RichText namespace migration test</para>
    <ezembed xlink:href="ezcontent://172" view="embed" ezxhtml:class="ez-embed-type-image"><ezconfig><ezvalue key="size">medium</ezvalue></ezconfig></ezembed>
</section>
XML
        ];
    }

    protected function createLandingPageContent(string $xml): Content
    {
        $landingPageCreateStruct = $this->getContentService()->newContentCreateStruct(
            $this->createLandingPageContentType(),
            self::DEFAULT_LANGUAGE_CODE
        );
        $landingPageCreateStruct->setField(
            'page',
            new Value(new Page('default', [$this->createZoneWithRichTextBlock($xml)]))
        );

        $contentDraft = $this->contentService->createContent($landingPageCreateStruct);

        return $this->contentService->publishVersion($contentDraft->getVersionInfo());
    }

    private function createZoneWithRichTextBlock(string $xml): Zone
    {
        return new Zone('1', 'Foo', [
            new BlockValue(
                '1',
                'richtext',
                'Text',
                'default',
                null,
                null,
                '',
                null,
                null,
                [
                    new Attribute(
                        '1',
                        'content',
                        $xml
                    ),
                ]
            ),
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function getXmlNamespacesMigrationMapParameter(): array
    {
        $xmlNamespacesMigrationMap = self::getContainer()
            ->getParameter('ibexa.field_type.rich_text.namespaces_migration_map');

        self::assertIsArray($xmlNamespacesMigrationMap);

        return $xmlNamespacesMigrationMap;
    }

    private function invalidateContentItemPersistenceCache(
        int $contentId,
        int $versionNo,
        string $languageCode
    ): void {
        /** @var \Symfony\Component\Cache\Adapter\TagAwareAdapter $cache */
        $cache = self::getContainer()->get('ibexa.cache_pool');

        $cache->invalidateTags(
            [
                sprintf('page-%d-%d-%s', $contentId, $versionNo, $languageCode),
            ]
        );
    }
}
