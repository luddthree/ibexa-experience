<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy\Mapper;

use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry as APITaxonomyEntry;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntryAssignment as APITaxonomyEntryAssignment;
use Ibexa\Core\Repository\ProxyFactory\ProxyDomainMapperInterface;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Taxonomy\Mapper\EntryAssignmentDomainMapper;
use Ibexa\Taxonomy\Persistence\Entity\TaxonomyEntry;
use Ibexa\Taxonomy\Persistence\Entity\TaxonomyEntryAssignment;
use Ibexa\Taxonomy\Proxy\ProxyDomainMapper;
use PHPUnit\Framework\TestCase;

final class EntryAssignmentDomainMapperTest extends TestCase
{
    private EntryAssignmentDomainMapper $domainMapper;

    /** @var \Ibexa\Core\Repository\ProxyFactory\ProxyDomainMapperInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ProxyDomainMapperInterface $proxyDomainMapper;

    /** @var \Ibexa\Taxonomy\Proxy\ProxyDomainMapper|\PHPUnit\Framework\MockObject\MockObject */
    private ProxyDomainMapper $entryProxyDomainMapper;

    protected function setUp(): void
    {
        $this->proxyDomainMapper = $this->createMock(ProxyDomainMapperInterface::class);
        $this->entryProxyDomainMapper = $this->createMock(ProxyDomainMapper::class);

        $this->domainMapper = new EntryAssignmentDomainMapper(
            $this->proxyDomainMapper,
            $this->entryProxyDomainMapper,
        );
    }

    public function testBuildDomainObjectFromPersistence(): void
    {
        $apiTaxonomyEntry = $this->createEntry();
        $taxonomyEntry = $this->createEntryEntity();
        $content = new Content();

        $this->proxyDomainMapper
            ->expects(self::once())
            ->method('createContentProxy')
            ->willReturn($content);
        $this->entryProxyDomainMapper
            ->expects(self::once())
            ->method('createEntryProxy')
            ->with(1)
            ->willReturn($apiTaxonomyEntry);

        self::assertEquals(
            new APITaxonomyEntryAssignment(
                3,
                $content,
                $apiTaxonomyEntry
            ),
            $this->domainMapper->buildDomainObjectFromPersistence(
                $this->createEntryAssignmentEntity($taxonomyEntry),
            ),
        );
    }

    private function createEntryAssignmentEntity(TaxonomyEntry $taxonomyEntry): TaxonomyEntryAssignment
    {
        $entryAssignment = new TaxonomyEntryAssignment();
        $entryAssignment->setId(3);
        $entryAssignment->setEntry($taxonomyEntry);
        $entryAssignment->setContent(2);

        return $entryAssignment;
    }

    private function createEntryEntity(): TaxonomyEntry
    {
        $taxonomyEntry = new TaxonomyEntry();
        $taxonomyEntry->setId(1);
        $taxonomyEntry->setIdentifier('foo');
        $taxonomyEntry->setName('Foo');
        $taxonomyEntry->setMainLanguageCode('eng-GB');
        $taxonomyEntry->setContentId(2);
        $taxonomyEntry->setTaxonomy('foobar');

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
