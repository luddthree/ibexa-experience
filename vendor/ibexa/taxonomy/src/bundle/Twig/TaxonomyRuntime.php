<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Taxonomy\Twig;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyEntryAssignmentServiceInterface;
use JMS\TranslationBundle\Annotation\Ignore;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\RuntimeExtensionInterface;

final class TaxonomyRuntime implements RuntimeExtensionInterface
{
    private const TAXONOMY_NAME_TRANSLATION_KEY = 'taxonomy.%s';

    private TranslatorInterface $translator;

    private TaxonomyEntryAssignmentServiceInterface $taxonomyEntryAssignmentService;

    public function __construct(
        TranslatorInterface $translator,
        TaxonomyEntryAssignmentServiceInterface $taxonomyEntryAssignmentService
    ) {
        $this->translator = $translator;
        $this->taxonomyEntryAssignmentService = $taxonomyEntryAssignmentService;
    }

    public function getTaxonomyName(?string $identifier): string
    {
        if ($identifier === null) {
            return '';
        }

        return $this->translator->trans(
            /** @Ignore */
            sprintf(self::TAXONOMY_NAME_TRANSLATION_KEY, $identifier),
            [],
            'ibexa_taxonomy'
        );
    }

    /**
     * @return array<\Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry>
     */
    public function getTaxonomyEntriesForContent(Content $content): array
    {
        $entries = [];
        $entryAssignmentsCollection = $this->taxonomyEntryAssignmentService->loadAssignments($content);
        foreach ($entryAssignmentsCollection->assignments as $entryAssignment) {
            $entries[] = $entryAssignment->entry;
        }

        return $entries;
    }
}
