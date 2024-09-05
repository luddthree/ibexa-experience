<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\REST\Input\Parser;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentStruct;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Rest\Input\Parser;
use Ibexa\CorporateAccount\REST\Exception\InputValidationFailedException;
use Ibexa\CorporateAccount\REST\Validation\Builder\Input\Parser\ContentFieldInputParserValidatorBuilder;
use Ibexa\Rest\Input\FieldTypeParser;
use Ibexa\Rest\RequestParser;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @internal
 *
 * @phpstan-type RESTContentFieldValueInputArray array{fieldDefinitionIdentifier: string, fieldValue: mixed}
 * @phpstan-type RESTResourceReferenceInputArray array{_media-type: string, _href: string}
 * @phpstan-type RESTContentFieldsInputArray array<RESTContentFieldValueInputArray>
 */
abstract class BaseContentParser extends Parser
{
    public const INTERNAL_URL_KEY = '__url';

    public const FIELDS_KEY = 'fields';

    public const FIELD_DEFINITION_IDENTIFIER_KEY = 'fieldDefinitionIdentifier';
    public const FIELD_VALUE_KEY = 'fieldValue';

    protected RequestParser $requestParser;

    private FieldTypeParser $fieldTypeParser;

    protected ValidatorInterface $validator;

    public function __construct(
        RequestParser $requestParser,
        FieldTypeParser $fieldTypeParser,
        ValidatorInterface $validator
    ) {
        $this->requestParser = $requestParser;
        $this->fieldTypeParser = $fieldTypeParser;
        $this->validator = $validator;
    }

    /**
     * @phpstan-param RESTContentFieldsInputArray $fieldsData
     */
    final protected function setContentFields(
        ContentStruct $contentStruct,
        ContentType $contentType,
        array $fieldsData
    ): void {
        if (empty($fieldsData)) {
            // validation for required Fields is deferred to PHP API
            return;
        }

        $this->assertContentFieldsInputIsValid($fieldsData, $contentType);

        foreach ($fieldsData as $fieldData) {
            $fieldDefinitionIdentifier = $fieldData[self::FIELD_DEFINITION_IDENTIFIER_KEY];
            /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition $fieldDefinition */
            $fieldDefinition = $contentType->getFieldDefinition($fieldDefinitionIdentifier);

            $contentStruct->setField(
                $fieldDefinitionIdentifier,
                $this->fieldTypeParser->parseValue(
                    $fieldDefinition->fieldTypeIdentifier,
                    $fieldData[self::FIELD_VALUE_KEY]
                )
            );
        }
    }

    /**
     * @phpstan-param RESTContentFieldsInputArray $fieldsData
     */
    private function assertContentFieldsInputIsValid(array $fieldsData, ContentType $contentType): void
    {
        $validatorBuilder = new ContentFieldInputParserValidatorBuilder($this->validator, $contentType);
        $validatorBuilder->validateInputArray($fieldsData);

        $violationList = $validatorBuilder->build()->getViolations();
        if ($violationList->count() > 0) {
            throw new InputValidationFailedException('fields', $violationList);
        }
    }
}
