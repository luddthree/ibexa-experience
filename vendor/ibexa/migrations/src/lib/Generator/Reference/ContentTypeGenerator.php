<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\Reference;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

class ContentTypeGenerator extends AbstractReferenceGenerator
{
    protected function getReferenceMetadata(): array
    {
        return [
            new ReferenceMetadata('ref', 'content_type_id'),
        ];
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType
     *
     * @return \Ibexa\Migration\Generator\Reference\ReferenceDefinition[]
     */
    public function generate(ValueObject $contentType): array
    {
        return $this->generateReferences($contentType->identifier, 'content_type_id');
    }
}

class_alias(ContentTypeGenerator::class, 'Ibexa\Platform\Migration\Generator\Reference\ContentTypeGenerator');
