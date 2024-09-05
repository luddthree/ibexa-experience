<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Output\ValueObjectVisitor;

use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;

final class Thumbnail extends ValueObjectVisitor
{
    private const OBJECT_IDENTIFIER = 'Thumbnail';
    private const ATTRIBUTE_IDENTIFIER_RESOURCE = 'resource';
    private const ATTRIBUTE_IDENTIFIER_WIDTH = 'width';
    private const ATTRIBUTE_IDENTIFIER_HEIGHT = 'height';
    private const ATTRIBUTE_IDENTIFIER_MIMETYPE = 'mimeType';

    /**
     * @param \Ibexa\Bundle\ProductCatalog\REST\Value\Thumbnail $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        if (empty($data->thumbnail)) {
            return;
        }

        $generator->startObjectElement(self::OBJECT_IDENTIFIER);
        $visitor->setHeader('Content-Type', $generator->getMediaType(self::OBJECT_IDENTIFIER));

        $generator->valueElement(self::ATTRIBUTE_IDENTIFIER_RESOURCE, $data->thumbnail->resource);
        $generator->valueElement(self::ATTRIBUTE_IDENTIFIER_WIDTH, $data->thumbnail->width);
        $generator->valueElement(self::ATTRIBUTE_IDENTIFIER_HEIGHT, $data->thumbnail->height);
        $generator->valueElement(self::ATTRIBUTE_IDENTIFIER_MIMETYPE, $data->thumbnail->mimeType);

        $generator->endObjectElement(self::OBJECT_IDENTIFIER);
    }
}
