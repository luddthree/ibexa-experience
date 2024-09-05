<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\DocumentSerializer;

use Ibexa\Contracts\Core\Search\Document;
use Ibexa\Core\Search\Common\FieldNameGenerator;
use Ibexa\Core\Search\Common\FieldValueMapper;

final class DocumentSerializer implements DocumentSerializerInterface
{
    /** @var \Ibexa\Core\Search\Common\FieldValueMapper */
    private $fieldValueMapper;

    /** @var \Ibexa\Core\Search\Common\FieldNameGenerator */
    private $nameGenerator;

    public function __construct(
        FieldValueMapper $fieldValueMapper,
        FieldNameGenerator $fieldNameGenerator
    ) {
        $this->fieldValueMapper = $fieldValueMapper;
        $this->nameGenerator = $fieldNameGenerator;
    }

    public function serialize(Document $document): array
    {
        $body = [];
        foreach ($document->fields as $field) {
            $fieldName = $this->nameGenerator->getTypedName($field->getName(), $field->getType());
            if ($this->fieldValueMapper->canMap($field)) {
                $fieldValue = $this->fieldValueMapper->map($field);
            } else {
                $fieldValue = $field->getValue();
            }

            $body[$fieldName] = $fieldValue;
        }

        return $body;
    }
}

class_alias(DocumentSerializer::class, 'Ibexa\Platform\ElasticSearchEngine\DocumentSerializer\DocumentSerializer');
