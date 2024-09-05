<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\GraphQL\Resolver;

use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Taxonomy\Translation\TaxonomyTranslationContainer;
use Ibexa\Taxonomy\Tree\TaxonomyTreeServiceInterface;
use JMS\TranslationBundle\Annotation\Ignore;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @internal
 */
final class TaxonomyResolver implements QueryInterface
{
    private TaxonomyTreeServiceInterface $taxonomyTreeService;

    private TaxonomyServiceInterface $taxonomyService;

    private TranslatorInterface $translator;

    public function __construct(
        TaxonomyTreeServiceInterface $taxonomyTreeService,
        TaxonomyServiceInterface $taxonomyService,
        TranslatorInterface $translator
    ) {
        $this->taxonomyTreeService = $taxonomyTreeService;
        $this->taxonomyService = $taxonomyService;
        $this->translator = $translator;
    }

    /**
     * @return array{
     *     identifier: string,
     *     name: string,
     *     root: \Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry,
     * }
     */
    public function resolveByIdentifier(string $identifier): array
    {
        $rootNodes = $this->taxonomyTreeService->loadTreeRoot($identifier);
        $root = reset($rootNodes);
        $rootEntry = $this->taxonomyService->loadEntryById($root['id']);

        return [
            'identifier' => $identifier,
            'name' => $this->translator->trans(
                /** @Ignore */
                sprintf('taxonomy.%s', $identifier),
                [],
                TaxonomyTranslationContainer::TRANSLATION_DOMAIN
            ),
            'root' => $rootEntry,
        ];
    }
}
