<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Connect\REST\Output;

use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;

final class TemplateVisitor extends ValueObjectVisitor
{
    private const OBJECT_IDENTIFIER = 'ScenarioBlockTemplate';

    /**
     * @param \Ibexa\Bundle\Connect\REST\Value\Template $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $generator->startObjectElement(self::OBJECT_IDENTIFIER);
        $visitor->setHeader('Content-Type', $generator->getMediaType(self::OBJECT_IDENTIFIER));

        $generator->valueElement('id', $data->id);
        $generator->valueElement('label', $data->label);
        $generator->valueElement('template', $data->template);

        $generator->startList('parameters');
        foreach ($data->parameters as $parameter) {
            $visitor->visitValueObject($parameter);
        }
        $generator->endList('parameters');

        $generator->endObjectElement(self::OBJECT_IDENTIFIER);
    }
}
