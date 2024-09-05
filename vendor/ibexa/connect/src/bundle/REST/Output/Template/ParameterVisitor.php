<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Connect\REST\Output\Template;

use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;

final class ParameterVisitor extends ValueObjectVisitor
{
    private const OBJECT_IDENTIFIER = 'ScenarioBlockTemplateParameter';

    /**
     * @param \Ibexa\Bundle\Connect\REST\Value\Template\Parameter $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $generator->startObjectElement(self::OBJECT_IDENTIFIER);
        $generator->valueElement('name', $data->name);
        $generator->valueElement('label', $data->label);
        $generator->valueElement('type', $data->type);
        $generator->valueElement('required', $generator->serializeBool($data->required));
        foreach ($data->options as $name => $option) {
            $generator->generateFieldTypeHash($name, $option);
        }
        $generator->endObjectElement(self::OBJECT_IDENTIFIER);
    }
}
