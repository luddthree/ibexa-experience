<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy\Mapper;

use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry as APITaxonomyEntry;
use Ibexa\Core\Repository\ProxyFactory\ProxyDomainMapperInterface;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Taxonomy\Mapper\EntryDomainMapper;
use Ibexa\Taxonomy\Persistence\Entity\TaxonomyEntry;
use PHPUnit\Framework\TestCase;

final class EntryDomainMapperTest extends TestCase
{
    private EntryDomainMapper $domainMapper;

    /** @var \Ibexa\Core\Repository\ProxyFactory\ProxyDomainMapperInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ProxyDomainMapperInterface $proxyDomainMapper;

    protected function setUp(): void
    {
        $this->proxyDomainMapper = $this->createMock(ProxyDomainMapperInterface::class);

        $this->domainMapper = new EntryDomainMapper($this->proxyDomainMapper);
    }

    public function testBuildDomainObjectFromPersistence(): void
    {
        $apiParentTaxonomyEntry = $this->createEntry();
        $taxonomyEntry = $this->createEntryEntity();
        $content = new Content();

        $this->proxyDomainMapper
            ->expects(self::once())
            ->method('createContentProxy')
            ->willReturn($content);

        self::assertEquals(
            new APITaxonomyEntry(
                1,
                'foo',
                'Foo',
                'eng-GB',
                [],
                $apiParentTaxonomyEntry,
                $content,
                'foobar',
                3,
                2,
            ),
            $this->domainMapper->buildDomainObjectFromPersistence(
                $taxonomyEntry,
                $apiParentTaxonomyEntry,
            ),
        );
    }

    private function createEntryEntity(): TaxonomyEntry
    {
        $taxonomyEntry = new TaxonomyEntry();
        $taxonomyEntry->setId(1);
        $taxonomyEntry->setIdentifier('foo');
        $taxonomyEntry->setName('Foo');
        $taxonomyEntry->setNames([]);
        $taxonomyEntry->setMainLanguageCode('eng-GB');
        $taxonomyEntry->setContentId(2);
        $taxonomyEntry->setTaxonomy('foobar');
        $taxonomyEntry->setLevel(3);

        return $taxonomyEntry;
    }

    private function createEntry(): APITaxonomyEntry
    {
        return new APITaxonomyEntry(
            1,
            'foo',
            'Foo',
            'eng-GB',
            [],
            null,
            new Content(),
            'foobar',
        );
    }
}
