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
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntryAssignmentCollection as EntryAssignmentCollectionValue;
use Ibexa\Rest\Server\Values\RestContent;

final class EntryAssignmentCollection extends ValueObjectVisitor
{
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        if (!$data instanceof EntryAssignmentCollectionValue) {
            return;
        }

        $generator->startObjectElement('EntryAssignmentCollection');

        $generator->startObjectElement('content', 'Content');
        $visitor->visitValueObject(new RestContent($data->content->contentInfo));
        $generator->endObjectElement('content');

        $generator->startList('assignments');
        foreach ($data->assignments as $assignment) {
            $visitor->visitValueObject($assignment);
        }
        $generator->endList('assignments');

        $generator->endObjectElement('EntryAssignmentCollection');
    }
}
