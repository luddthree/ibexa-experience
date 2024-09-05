<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\Reference;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

final class ContentTypeGroupGenerator extends AbstractReferenceGenerator
{
    protected function getReferenceMetadata(): array
    {
        return [
            new ReferenceMetadata('ref', 'content_type_group_id'),
        ];
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup $contentTypeGroup
     *
     * @return \Ibexa\Migration\Generator\Reference\ReferenceDefinition[]
     */
    public function generate(ValueObject $contentTypeGroup): array
    {
        return $this->generateReferences($contentTypeGroup->identifier, 'content_type_group_id');
    }
}

class_alias(ContentTypeGroupGenerator::class, 'Ibexa\Platform\Migration\Generator\Reference\ContentTypeGroupGenerator');
