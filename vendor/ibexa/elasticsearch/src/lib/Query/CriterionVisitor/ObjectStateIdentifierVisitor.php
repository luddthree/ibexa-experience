<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\CriterionVisitor;

use Ibexa\Contracts\Core\Persistence\Content\ObjectState\Group;
use Ibexa\Contracts\Core\Persistence\Content\ObjectState\Handler as ObjectStateHandler;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ObjectStateIdentifier;
use Ibexa\Contracts\Elasticsearch\Query\CriterionVisitor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\TermsQuery;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

final class ObjectStateIdentifierVisitor implements CriterionVisitor, LoggerAwareInterface
{
    use LoggerAwareTrait;

    // TODO: Dodać dedykowane pole object_state_identifiers_ids do indeksu
    private const INDEX_FIELD = 'object_state_id_mid';

    /** @var \Ibexa\Contracts\Core\Persistence\Content\ObjectState\Handler */
    private $objectStateHandler;

    public function __construct(ObjectStateHandler $objectStateHandler)
    {
        $this->objectStateHandler = $objectStateHandler;
        $this->logger = new NullLogger();
    }

    public function supports(Criterion $criterion, LanguageFilter $languageFilter): bool
    {
        return $criterion instanceof ObjectStateIdentifier;
    }

    public function visit(CriterionVisitor $dispatcher, Criterion $criterion, LanguageFilter $languageFilter): array
    {
        $identifiers = (array)$criterion->value;

        if (empty($criterion->target)) {
            $validIds = $this->getObjectStateIds($identifiers);
        } else {
            $validIds = $this->getObjectStateIdsForGroup(
                $identifiers,
                $this->objectStateHandler->loadGroupByIdentifier($criterion->target)
            );
        }

        if (empty($validIds)) {
            return (new TermsQuery(self::INDEX_FIELD, [-1]))->toArray();
        }

        return (new TermsQuery(self::INDEX_FIELD, $validIds))->toArray();
    }

    private function getObjectStateIds(array $identifiers): array
    {
        $validIds = [];
        $validIdentifiers = [];

        $objectStateGroups = $this->objectStateHandler->loadAllGroups();
        foreach ($objectStateGroups as $objectStateGroup) {
            foreach ($identifiers as $identifier) {
                try {
                    $validIds[] = $this->objectStateHandler->loadByIdentifier(
                        $identifier,
                        $objectStateGroup->id
                    )->id;

                    if (!in_array($identifier, $validIdentifiers)) {
                        $validIdentifiers[] = $identifier;
                    }
                } catch (NotFoundException $e) {
                    // Ignored on purpose
                }
            }
        }

        if (count($validIdentifiers) !== count($identifiers)) {
            $this->logger->warning(
                sprintf(
                    'Invalid object state identifiers provided for ObjectStateIdentifier criterion: %s',
                    implode(', ', array_diff($identifiers, $validIdentifiers))
                )
            );
        }

        return $validIds;
    }

    private function getObjectStateIdsForGroup(array $identifiers, Group $group): array
    {
        $validIds = [];

        $invalidIdentifiers = [];
        foreach ($identifiers as $identifier) {
            try {
                $validIds[] = $this->objectStateHandler->loadByIdentifier($identifier, $group->id)->id;
            } catch (NotFoundException $e) {
                $invalidIdentifiers[] = $identifier;
            }
        }

        if (!empty($invalidIdentifiers)) {
            $this->logger->warning(
                sprintf(
                    'Invalid object state identifiers provided for ObjectStateIdentifier criterion: %s',
                    implode(', ', $invalidIdentifiers)
                )
            );
        }

        return $validIds;
    }
}

class_alias(ObjectStateIdentifierVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\CriterionVisitor\ObjectStateIdentifierVisitor');
