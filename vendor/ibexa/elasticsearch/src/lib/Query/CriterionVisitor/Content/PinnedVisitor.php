<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\CriterionVisitor\Content;

use Ibexa\Contracts\Core\Persistence\Content\Handler as ContentHandler;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Elasticsearch\DocumentMapper\DocumentIdGeneratorInterface;
use Ibexa\Elasticsearch\Query\CriterionVisitor\AbstractPinnedVisitor;

final class PinnedVisitor extends AbstractPinnedVisitor
{
    /** @var \Ibexa\Elasticsearch\DocumentMapper\DocumentIdGeneratorInterface */
    private $documentIdGenerator;

    /** @var \Ibexa\Contracts\Core\Persistence\Content\Handler */
    private $contentHandler;

    public function __construct(DocumentIdGeneratorInterface $documentIdGenerator, ContentHandler $contentHandler)
    {
        $this->documentIdGenerator = $documentIdGenerator;
        $this->contentHandler = $contentHandler;
    }

    protected function getAlwaysAvailableLanguage(int $id): ?string
    {
        try {
            return $this->contentHandler->loadContentInfo($id)->mainLanguageCode;
        } catch (NotFoundException $e) {
            return null;
        }
    }

    protected function getDocumentId(int $id, string $languageCode): string
    {
        return $this->documentIdGenerator->generateContentDocumentId($id, $languageCode);
    }
}

class_alias(PinnedVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\CriterionVisitor\Content\PinnedVisitor');
