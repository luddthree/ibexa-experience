<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Proxy;

use Ibexa\Contracts\Taxonomy\Service\TaxonomyEntryAssignmentServiceInterface;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntryAssignment;
use Ibexa\Core\Repository\ProxyFactory\ProxyGeneratorInterface;
use ProxyManager\Proxy\LazyLoadingInterface;

/**
 * @internal
 */
class ProxyDomainMapper
{
    private TaxonomyServiceInterface $taxonomyService;

    private TaxonomyEntryAssignmentServiceInterface $taxonomyEntryAssignmentService;

    private ProxyGeneratorInterface $proxyGenerator;

    public function __construct(
        TaxonomyServiceInterface $taxonomyService,
        TaxonomyEntryAssignmentServiceInterface $taxonomyEntryAssignmentService,
        ProxyGeneratorInterface $proxyGenerator
    ) {
        $this->taxonomyService = $taxonomyService;
        $this->taxonomyEntryAssignmentService = $taxonomyEntryAssignmentService;
        $this->proxyGenerator = $proxyGenerator;
    }

    public function createEntryProxy(int $entryId): TaxonomyEntry
    {
        $initializer = function (
            &$wrappedObject,
            LazyLoadingInterface $proxy,
            $method,
            array $parameters,
            &$initializer
        ) use ($entryId): bool {
            $initializer = null;
            $wrappedObject = $this->taxonomyService->loadEntryById($entryId);

            return true;
        };

        return $this->proxyGenerator->createProxy(TaxonomyEntry::class, $initializer);
    }

    public function createEntryAssignmentProxy(int $entryAssignmentId): TaxonomyEntryAssignment
    {
        $initializer = function (
            &$wrappedObject,
            LazyLoadingInterface $proxy,
            $method,
            array $parameters,
            &$initializer
        ) use ($entryAssignmentId): bool {
            $initializer = null;
            $wrappedObject = $this->taxonomyEntryAssignmentService->loadAssignmentById($entryAssignmentId);

            return true;
        };

        return $this->proxyGenerator->createProxy(TaxonomyEntryAssignment::class, $initializer);
    }
}
