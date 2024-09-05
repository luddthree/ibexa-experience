<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\CriterionVisitor;

use Ibexa\Contracts\Core\Persistence\Content\Type\Handler;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use Ibexa\Contracts\Elasticsearch\Query\CriterionVisitor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\TermQuery;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\TermsQuery;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

final class ContentTypeIdentifierVisitor implements CriterionVisitor, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private const INDEX_FIELD = 'content_type_id_id';

    /** @var \Ibexa\Contracts\Core\Persistence\Content\Type\Handler */
    private $contentTypeHandler;

    public function __construct(Handler $contentTypeHandler)
    {
        $this->contentTypeHandler = $contentTypeHandler;
        $this->logger = new NullLogger();
    }

    public function supports(Criterion $criterion, LanguageFilter $languageFilter): bool
    {
        return $criterion instanceof ContentTypeIdentifier;
    }

    public function visit(CriterionVisitor $dispatcher, Criterion $criterion, LanguageFilter $languageFilter): array
    {
        $validIds = [];

        $invalidIdentifiers = [];
        foreach ($criterion->value as $identifier) {
            try {
                $validIds[] = $this->contentTypeHandler->loadByIdentifier($identifier)->id;
            } catch (NotFoundException $e) {
                // Filter out non-existing content types, but track for code below
                $invalidIdentifiers[] = $identifier;
            }
        }

        if (count($invalidIdentifiers) > 0) {
            $this->logger->warning(
                sprintf(
                    'Invalid content type identifiers provided for ContentTypeIdentifier criterion: %s',
                    implode(', ', $invalidIdentifiers)
                )
            );
        }

        if (count($validIds) === 0) {
            return (new TermQuery(self::INDEX_FIELD, -1))->toArray();
        }

        return (new TermsQuery(self::INDEX_FIELD, $validIds))->toArray();
    }
}

class_alias(ContentTypeIdentifierVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\CriterionVisitor\ContentTypeIdentifierVisitor');
