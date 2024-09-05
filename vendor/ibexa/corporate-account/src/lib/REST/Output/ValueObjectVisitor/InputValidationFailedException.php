<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\REST\Output\ValueObjectVisitor;

use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\Visitor;
use Ibexa\Rest\Server\Output\ValueObjectVisitor\BadRequestException;

/**
 * @internal
 */
final class InputValidationFailedException extends BadRequestException
{
    /**
     * @param \Ibexa\CorporateAccount\REST\Exception\InputValidationFailedException $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $generator->startObjectElement('ErrorMessage');

        $statusCode = $this->getStatus();
        $visitor->setStatus($statusCode);
        $visitor->setHeader('Content-Type', $generator->getMediaType('ErrorMessage'));

        $generator->valueElement('errorCode', $statusCode);

        $generator->valueElement(
            'errorMessage',
            self::$httpStatusCodes[$statusCode] ?? self::$httpStatusCodes[500]
        );
        $generator->valueElement('errorDescription', $data->getMessage());

        $generator->startList('violationList');
        foreach ($data->getErrors() as $error) {
            $generator->startHashElement('violation');
            $generator->valueElement('message', $error->getMessage());
            $generator->valueElement('propertyPath', $error->getPropertyPath());
            $parameters = $error->getParameters();

            $generator->startList('parameters');
            foreach ($parameters as $key => $value) {
                $generator->startHashElement('parameter');
                $generator->attribute('key', $key);
                $generator->attribute('value', $value);
                $generator->endHashElement('parameter');
            }
            $generator->endList('parameters');

            $generator->endHashElement('violation');
        }
        $generator->endList('violationList');

        if ($this->debug) {
            $generator->valueElement('trace', $data->getTraceAsString());
            $generator->valueElement('file', $data->getFile());
            $generator->valueElement('line', $data->getLine());
        }

        $generator->endObjectElement('ErrorMessage');
    }
}
