<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\REST\Output\ValueObjectVisitor;

use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntryAssignment as ValueEntryAssignment;
use Ibexa\Rest\Server\Values\RestContent;
use Ibexa\Taxonomy\REST\Values\RestTaxonomyEntry;

final class TaxonomyEntryAssignment extends ValueObjectVisitor
{
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        if (!$data instanceof ValueEntryAssignment) {
            return;
        }

        $generator->startObjectElement('TaxonomyEntryAssignment');

        $generator->valueElement('id', $data->id);

        $generator->startObjectElement('content', 'Content');
        $visitor->visitValueObject(new RestContent($data->content->contentInfo));
        $generator->endObjectElement('content');

        $generator->startObjectElement('entry', 'TaxonomyEntry');
        $visitor->visitValueObject(new RestTaxonomyEntry($data->entry));
        $generator->endObjectElement('entry');

        $generator->endObjectElement('TaxonomyEntryAssignment');
    }
}
