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
use Ibexa\Taxonomy\GraphQL\Resolver\TaxonomyResolver;
use Ibexa\Taxonomy\Tree\TaxonomyTreeServiceInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

final class TaxonomyResolverTest extends TestCase
{
    private TaxonomyResolver $resolver;

    /** @var \Ibexa\Taxonomy\Tree\TaxonomyTreeServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private TaxonomyTreeServiceInterface $taxonomyTreeService;

    /** @var \Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private TaxonomyServiceInterface $taxonomyService;

    /** @var \Symfony\Contracts\Translation\TranslatorInterface|\PHPUnit\Framework\MockObject\MockObject */
    private TranslatorInterface $translator;

    protected function setUp(): void
    {
        $this->taxonomyTreeService = $this->createMock(TaxonomyTreeServiceInterface::class);
        $this->taxonomyService = $this->createMock(TaxonomyServiceInterface::class);
        $this->translator = $this->createMock(TranslatorInterface::class);

        $this->resolver = new TaxonomyResolver(
            $this->taxonomyTreeService,
            $this->taxonomyService,
            $this->translator
        );
    }

    public function testResolveByIdentifier(): void
    {
        $identifier = 'foobar';
        $treeRootNode = ['id' => 123];
        $rootEntry = new TaxonomyEntry(
            123,
            'root',
            'Root',
            'eng-GB',
            [],
            null,
            new Content(),
            'foobar',
        );

        $this->translator
            ->method('trans')
            ->willReturn('Foobar');

        $this->taxonomyTreeService
            ->expects(self::once())
            ->method('loadTreeRoot')
            ->with($identifier)
            ->willReturn([$treeRootNode]);

        $this->taxonomyService
            ->expects(self::once())
            ->method('loadEntryById')
            ->with(123)
            ->willReturn($rootEntry);

        $expectedResult = [
            'identifier' => 'foobar',
            'name' => 'Foobar',
            'root' => $rootEntry,
        ];

        self::assertEquals(
            $expectedResult,
            $this->resolver->resolveByIdentifier($identifier),
        );
    }
}
