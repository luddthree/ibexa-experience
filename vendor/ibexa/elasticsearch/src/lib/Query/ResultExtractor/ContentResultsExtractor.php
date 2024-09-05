<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\ResultExtractor;

use Ibexa\Contracts\Core\Persistence\Content\Handler as ContentHandler;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\Elasticsearch\Query\AggregationResultExtractor;
use Ibexa\Contracts\Elasticsearch\Query\FacetResultExtractor;

final class ContentResultsExtractor extends AbstractResultsExtractor
{
    public const CONTENT_ID_FIELD = 'content_id_id';

    /** @var \Ibexa\Contracts\Core\Persistence\Content\Handler */
    private $contentHandler;

    public function __construct(
        ContentHandler $contentHandler,
        FacetResultExtractor $facetResultExtractor,
        AggregationResultExtractor $aggregationResultExtractor,
        bool $skipMissingContentItems = true
    ) {
        parent::__construct($facetResultExtractor, $aggregationResultExtractor, $skipMissingContentItems);

        $this->contentHandler = $contentHandler;
    }

    protected function loadValueObject(array $document): ValueObject
    {
        return $this->contentHandler->loadContentInfo(
            (int)$document[self::CONTENT_ID_FIELD]
        );
    }

    public function getExpectedSourceFields(): array
    {
        return [
            self::MATCHED_TRANSLATION_FIELD,
            self::CONTENT_ID_FIELD,
        ];
    }
}

class_alias(ContentResultsExtractor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\ResultExtractor\ContentResultsExtractor');
