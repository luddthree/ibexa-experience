<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Taxonomy\Controller;

use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Taxonomy\Tree\TaxonomyTreeServiceInterface;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class TreeController extends Controller
{
    private TaxonomyTreeServiceInterface $taxonomyTreeService;

    private LanguageService $languageService;

    public function __construct(
        TaxonomyTreeServiceInterface $taxonomyTreeService,
        LanguageService $languageService
    ) {
        $this->taxonomyTreeService = $taxonomyTreeService;
        $this->languageService = $languageService;
    }

    public function getRootAction(string $taxonomyName): JsonResponse
    {
        $treeRoot = $this->taxonomyTreeService->loadTreeRoot($taxonomyName);

        return $this->json($treeRoot);
    }

    public function getNodeAction(int $entryId): JsonResponse
    {
        $node = $this->taxonomyTreeService->loadNode($entryId);

        return $this->json($node);
    }

    public function getSubtreeAction(Request $request): JsonResponse
    {
        /** @var array<int> $entryIds */
        $entryIds = $request->query->get('entryIds');

        $tree = $this->taxonomyTreeService->loadSubtree((array) $entryIds);

        return $this->json($tree);
    }

    public function nodeSearchAction(Request $request, string $taxonomyName): JsonResponse
    {
        $query = $request->query->get('query');
        $languageCode = $request->query->get('languageCode');

        if (!is_string($query) || strlen($query) < 1) {
            throw new InvalidArgumentException('Query has to be at least 1 character long');
        }

        if (null !== $languageCode) {
            try {
                $this->languageService->loadLanguage($languageCode);
            } catch (NotFoundException $exception) {
                throw new InvalidArgumentException('Invalid language code provided', 0, $exception);
            }
        }

        $tree = $this->taxonomyTreeService->findNodes(
            $query,
            $languageCode,
            TaxonomyTreeServiceInterface::DEFAULT_LIMIT,
            0,
            $taxonomyName,
        );

        return $this->json($tree);
    }
}
