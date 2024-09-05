<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Taxonomy\Twig;

use Ibexa\Bundle\Taxonomy\Twig\TaxonomyExtension;
use Ibexa\Bundle\Taxonomy\Twig\TaxonomyRuntime;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyEntryAssignmentServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntryAssignment;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntryAssignmentCollection;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\RuntimeExtensionInterface;
use Twig\RuntimeLoader\RuntimeLoaderInterface;
use Twig\Test\IntegrationTestCase;

final class TaxonomyExtensionTest extends IntegrationTestCase
{
    protected function getRuntimeLoaders(): array
    {
        $translator = $this->createTranslator();
        $taxonomyEntryAssignmentService = $this->createTaxonomyEntryAssignmentService();

        return [
            new class($translator, $taxonomyEntryAssignmentService) implements RuntimeLoaderInterface {
                private TranslatorInterface $translator;

                private TaxonomyEntryAssignmentServiceInterface $taxonomyEntryAssignmentService;

                public function __construct(
                    TranslatorInterface $translator,
                    TaxonomyEntryAssignmentServiceInterface $taxonomyEntryAssignmentService
                ) {
                    $this->translator = $translator;
                    $this->taxonomyEntryAssignmentService = $taxonomyEntryAssignmentService;
                }

                public function load(string $class): ?RuntimeExtensionInterface
                {
                    if ($class === TaxonomyRuntime::class) {
                        return new TaxonomyRuntime($this->translator, $this->taxonomyEntryAssignmentService);
                    }

                    return null;
                }
            },
        ];
    }

    protected function getFixturesDir(): string
    {
        return __DIR__ . '/fixtures/';
    }

    /**
     * @return \Twig\Extension\ExtensionInterface[]
     */
    protected function getExtensions(): array
    {
        return [
            new TaxonomyExtension(),
        ];
    }

    private function createTranslator(): TranslatorInterface
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator
            ->method('trans')
            ->with('taxonomy.category', [], 'ibexa_taxonomy')
            ->willReturn('Product category');

        return $translator;
    }

    private function createTaxonomyEntryAssignmentService(): TaxonomyEntryAssignmentServiceInterface
    {
        $content = $this->createMock(Content::class);
        $collection = new TaxonomyEntryAssignmentCollection(
            $content,
            [
                $this->getTaxonomyEntryAssignment('foo'),
                $this->getTaxonomyEntryAssignment('bar'),
            ]
        );
        $translator = $this->createMock(TaxonomyEntryAssignmentServiceInterface::class);
        $translator
            ->method('loadAssignments')
            ->willReturn($collection);

        return $translator;
    }

    public function getContent(): Content
    {
        return $this->createMock(Content::class);
    }

    private function getTaxonomyEntryAssignment(string $name): TaxonomyEntryAssignment
    {
        $entry = $this->createMock(TaxonomyEntry::class);
        $entry
            ->method('getName')
            ->willReturn($name);

        $assigment = $this->createMock(TaxonomyEntryAssignment::class);
        $assigment
            ->method('__get')
            ->with('entry')
            ->willReturn($entry);

        return $assigment;
    }
}
