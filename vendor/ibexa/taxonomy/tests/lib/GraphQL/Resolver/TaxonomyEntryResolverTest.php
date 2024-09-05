<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy\GraphQL\Resolver;

use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Taxonomy\GraphQL\Resolver\TaxonomyEntryResolver;
use InvalidArgumentException;
use Overblog\GraphQLBundle\Definition\Argument;
use PHPUnit\Framework\TestCase;

final class TaxonomyEntryResolverTest extends TestCase
{
    private TaxonomyEntryResolver $resolver;

    /** @var \Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private TaxonomyServiceInterface $taxonomyService;

    protected function setUp(): void
    {
        $this->taxonomyService = $this->createMock(TaxonomyServiceInterface::class);

        $this->resolver = new TaxonomyEntryResolver($this->taxonomyService);
    }

    public function testResolveTaxonomyEntryWithId(): void
    {
        $entry = $this->createTaxonomyEntry();

        $this->taxonomyService
            ->expects(self::once())
            ->method('loadEntryById')
            ->with(123)
            ->willReturn($entry);

        $args = $this->createMock(Argument::class);

        $args
            ->method('offsetExists')
            ->withConsecutive(['id'])
            ->willReturnOnConsecutiveCalls(true);

        $args
            ->method('offsetGet')
            ->with('id')
            ->willReturn(123);

        self::assertEquals($entry, $this->resolver->resolveTaxonomyEntry($args));
    }

    public function testResolveTaxonomyEntryWithContentId(): void
    {
        $entry = $this->createTaxonomyEntry();

        $this->taxonomyService
            ->expects(self::once())
            ->method('loadEntryByContentId')
            ->with(456)
            ->willReturn($entry);

        $args = $this->createMock(Argument::class);

        $args
            ->method('offsetExists')
            ->withConsecutive(['id'], ['identifier'], ['contentId'])
            ->willReturnOnConsecutiveCalls(false, false, true);

        $args
            ->method('offsetGet')
            ->with('contentId')
            ->willReturn(456);

        self::assertEquals($entry, $this->resolver->resolveTaxonomyEntry($args));
    }

    public function testResolveTaxonomyEntryWithIdentifier(): void
    {
        $entry = $this->createTaxonomyEntry();

        $this->taxonomyService
            ->expects(self::once())
            ->method('loadEntryByIdentifier')
            ->with('foo')
            ->willReturn($entry);

        $args = $this->createMock(Argument::class);

        $args
            ->method('offsetExists')
            ->withConsecutive(['id'], ['identifier'])
            ->willReturnOnConsecutiveCalls(false, true);

        $args
            ->method('offsetGet')
            ->with('identifier')
            ->willReturn('foo');

        self::assertEquals($entry, $this->resolver->resolveTaxonomyEntry($args));
    }

    public function testResolveTaxonomyEntryThrowOnInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot resolve TaxonomyEntry from argument set');

        $args = $this->createMock(Argument::class);

        $args
            ->method('offsetExists')
            ->withConsecutive(['id'], ['identifier'], ['contentId'])
            ->willReturnOnConsecutiveCalls(false, false, false);

        $this->resolver->resolveTaxonomyEntry($args);
    }

    private function createTaxonomyEntry(): TaxonomyEntry
    {
        return new TaxonomyEntry(
            123,
            'foo',
            'Foo',
            'eng-GB',
            [],
            null,
            new Content(),
            'bar',
        );
    }
}
