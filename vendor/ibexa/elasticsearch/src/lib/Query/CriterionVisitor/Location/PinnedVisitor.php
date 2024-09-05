<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\CriterionVisitor\Location;

use Ibexa\Contracts\Core\Persistence\Content\Handler as ContentHandler;
use Ibexa\Contracts\Core\Persistence\Content\Location\Handler as LocationHandler;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Elasticsearch\DocumentMapper\DocumentIdGeneratorInterface;
use Ibexa\Elasticsearch\Query\CriterionVisitor\AbstractPinnedVisitor;

final class PinnedVisitor extends AbstractPinnedVisitor
{
    /** @var \Ibexa\Elasticsearch\DocumentMapper\DocumentIdGeneratorInterface */
    private $documentIdGenerator;

    /** @var \Ibexa\Contracts\Core\Persistence\Content\Handler */
    private $contentHandler;

    /** @var \Ibexa\Contracts\Core\Persistence\Content\Location\Handler */
    private $locationHandler;

    public function __construct(
        DocumentIdGeneratorInterface $documentIdGenerator,
        ContentHandler $contentHandler,
        LocationHandler $locationHandler
    ) {
        $this->documentIdGenerator = $documentIdGenerator;
        $this->contentHandler = $contentHandler;
        $this->locationHandler = $locationHandler;
    }

    protected function getDocumentId(int $id, string $languageCode): string
    {
        return $this->documentIdGenerator->generateLocationDocumentId($id, $languageCode);
    }

    protected function getAlwaysAvailableLanguage(int $id): ?string
    {
        try {
            return $this->contentHandler->loadContentInfo(
                $this->locationHandler->load($id)->contentId
            )->mainLanguageCode;
        } catch (NotFoundException $e) {
            return null;
        }
    }
}

class_alias(PinnedVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\CriterionVisitor\Location\PinnedVisitor');
