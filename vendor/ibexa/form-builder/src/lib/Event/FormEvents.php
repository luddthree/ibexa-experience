<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Event;

final class FormEvents
{
    public const FIELD_DEFINITION = 'ezplatform.form_builder.field';
    public const FIELD_DEFINITION_ATTRIBUTE = 'ezplatform.form_builder.field.attribute';

    public const FIELD_DEFINITION_PATTERN = 'ezplatform.form_builder.field.%s';
    public const FIELD_ATTRIBUTE_DEFINITION_PATTERN = 'ezplatform.form_builder.field.%s.attribute.%s';
    public const FIELD_VALIDATOR_DEFINITION_PATTERN = 'ezplatform.form_builder.field.%s.validator.%s';

    public const FORM_SUBMIT = 'ezplatform.form_builder.form_submit';
    public const FORM_SUBMIT_CONTENT_PATTERN = 'ezplatform.form_builder.form_submit.content_id.%d';
    public const FORM_SUBMIT_ACTION_PATTERN = 'ezplatform.form_builder.form_submit.action.%s';

    /**
     * @param string $fieldIdentifier
     *
     * @return string
     */
    public static function getFieldDefinitionEventName(string $fieldIdentifier): string
    {
        return sprintf(self::FIELD_DEFINITION_PATTERN, $fieldIdentifier);
    }

    /**
     * @param string $fieldIdentifier
     * @param string $attributeIdentifier
     *
     * @return string
     */
    public static function getFieldAttributeDefinitionEventName(
        string $fieldIdentifier,
        string $attributeIdentifier
    ): string {
        return sprintf(self::FIELD_ATTRIBUTE_DEFINITION_PATTERN, $fieldIdentifier, $attributeIdentifier);
    }

    /**
     * @param string $fieldIdentifier
     * @param $validatorIdentifier
     *
     * @return string
     */
    public static function getFieldValidatorDefinitionEventName(
        string $fieldIdentifier,
        $validatorIdentifier
    ) {
        return sprintf(self::FIELD_VALIDATOR_DEFINITION_PATTERN, $fieldIdentifier, $validatorIdentifier);
    }

    /**
     * @param string $actionName
     *
     * @return string
     */
    public static function getSubmitActionEventName(
        string $actionName
    ) {
        return sprintf(self::FORM_SUBMIT_ACTION_PATTERN, $actionName);
    }

    /**
     * @param int $contentId
     *
     * @return string
     */
    public static function getSubmitContentEventName(
        int $contentId
    ) {
        return sprintf(self::FORM_SUBMIT_CONTENT_PATTERN, $contentId);
    }
}

class_alias(FormEvents::class, 'EzSystems\EzPlatformFormBuilder\Event\FormEvents');
