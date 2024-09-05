<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step;

use Ibexa\Migration\ValueObject\ContentTypeGroup\CreateMetadata;

final class ContentTypeGroupCreateStep implements ActionsAwareStepInterface, ReferenceAwareStepInterface
{
    use ActionsAwareStepTrait;
    use ReferenceAwareStepTrait;

    /** @var \Ibexa\Migration\ValueObject\ContentTypeGroup\CreateMetadata */
    public $metadata;

    /**
     * @param \Ibexa\Migration\Generator\Reference\ReferenceDefinition[] $references
     */
    public function __construct(CreateMetadata $metadata, array $references = [])
    {
        $this->metadata = $metadata;
        $this->references = $references;
    }
}

class_alias(ContentTypeGroupCreateStep::class, 'Ibexa\Platform\Migration\ValueObject\Step\ContentTypeGroupCreateStep');
