<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step;

use Ibexa\Migration\ValueObject\ContentType\CreateMetadata;
use Ibexa\Migration\ValueObject\ContentType\FieldDefinitionCollection;

final class ContentTypeCreateStep implements ActionsAwareStepInterface, ReferenceAwareStepInterface, UserContextAwareStepInterface
{
    use ActionsAwareStepTrait;
    use ReferenceAwareStepTrait;

    /** @var \Ibexa\Migration\ValueObject\ContentType\CreateMetadata */
    private $metadata;

    /**
     * @var \Ibexa\Migration\ValueObject\ContentType\FieldDefinitionCollection
     */
    private $fields;

    /**
     * @param \Ibexa\Migration\Generator\Reference\ReferenceDefinition[] $references
     */
    public function __construct(CreateMetadata $metadata, FieldDefinitionCollection $fields, array $references = [])
    {
        $this->metadata = $metadata;
        $this->fields = $fields;
        $this->references = $references;
    }

    public function getMetadata(): CreateMetadata
    {
        return $this->metadata;
    }

    /**
     * @phpstan-return \Ibexa\Migration\ValueObject\ContentType\FieldDefinitionCollection<
     *     \Ibexa\Migration\ValueObject\ContentType\FieldDefinition
     * >
     */
    public function getFields(): FieldDefinitionCollection
    {
        return $this->fields;
    }

    public function getUserId(): ?int
    {
        return $this->metadata->creatorId;
    }
}

class_alias(ContentTypeCreateStep::class, 'Ibexa\Platform\Migration\ValueObject\Step\ContentTypeCreateStep');
