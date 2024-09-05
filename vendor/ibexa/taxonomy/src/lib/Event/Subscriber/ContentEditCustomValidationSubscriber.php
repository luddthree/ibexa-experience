<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Event\Subscriber;

use Ibexa\ContentForms\Event\ContentCreateFieldOptionsEvent;
use Ibexa\ContentForms\Event\ContentFormEvents;
use Ibexa\ContentForms\Event\ContentUpdateFieldOptionsEvent;
use Ibexa\Contracts\ContentForms\Data\Content\FieldData;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Taxonomy\Exception\TaxonomyNotFoundException;
use Ibexa\Taxonomy\Form\Type\Validation\Constraint\Nested;
use Ibexa\Taxonomy\Form\Type\Validation\Constraint\UniqueIdentifier;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Adds custom validators to Taxonomy fields in Content update/create form.
 *
 * @internal
 */
final class ContentEditCustomValidationSubscriber implements EventSubscriberInterface
{
    private const MAX_IDENTIFIER_LENGTH = 190;
    private const MAX_NAME_LENGTH = 255;

    private TaxonomyConfiguration $taxonomyConfiguration;

    public function __construct(
        TaxonomyConfiguration $taxonomyConfiguration
    ) {
        $this->taxonomyConfiguration = $taxonomyConfiguration;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ContentFormEvents::CONTENT_EDIT_FIELD_OPTIONS => 'onEditFieldOptions',
            ContentFormEvents::CONTENT_CREATE_FIELD_OPTIONS => 'onCreateFieldOptions',
        ];
    }

    public function onEditFieldOptions(ContentUpdateFieldOptionsEvent $event): void
    {
        $contentType = $event->getContent()->getContentType();

        $event->setOptions(
            $this->attachValidator($contentType, $event->getFieldData(), $event->getOptions())
        );
    }

    public function onCreateFieldOptions(ContentCreateFieldOptionsEvent $event): void
    {
        $contentType = $event->getContentCreateStruct()->contentType;

        $event->setOptions(
            $this->attachValidator($contentType, $event->getFieldData(), $event->getOptions())
        );
    }

    /**
     * @param array<string, mixed> $options
     *
     * @return array<string, mixed>
     */
    private function attachValidator(ContentType $contentType, FieldData $fieldData, array $options): array
    {
        try {
            $taxonomy = $this->taxonomyConfiguration->getTaxonomyForContentType($contentType);
            $fieldMappings = $this->taxonomyConfiguration->getFieldMappings($taxonomy);
        } catch (TaxonomyNotFoundException $e) {
            return $options;
        }

        if ($fieldData->fieldDefinition->identifier === $fieldMappings['identifier']) {
            return $this->doAttachValidatorToIdentifier($options, $taxonomy);
        }

        if ($fieldData->fieldDefinition->identifier === $fieldMappings['name']) {
            return $this->doAttachValidatorToName($options);
        }

        return $options;
    }

    /**
     * @param array<string, mixed> $options
     *
     * @return array<string, mixed>
     */
    private function doAttachValidatorToName(array $options): array
    {
        $options['constraints'] = array_merge(
            $options['constraints'] ?? [],
            [
                $this->createNestedConstraints(
                    [
                        new NotBlank(),
                        new Length(['max' => self::MAX_NAME_LENGTH]),
                    ]
                ),
            ]
        );

        $options['error_mapping'] = array_merge(
            $options['error_mapping'] ?? [],
            ['.' => 'value']
        );

        return $options;
    }

    /**
     * @param array<string, mixed> $options
     *
     * @return array<string, mixed>
     */
    private function doAttachValidatorToIdentifier(array $options, string $taxonomy): array
    {
        $options['constraints'] = array_merge(
            $options['constraints'] ?? [],
            [
                $this->createNestedConstraints(
                    [
                        new NotBlank(),
                        new Length(['max' => self::MAX_IDENTIFIER_LENGTH]),
                    ]
                ),
                new UniqueIdentifier(['taxonomy' => $taxonomy]),
            ]
        );

        $options['error_mapping'] = array_merge(
            $options['error_mapping'] ?? [],
            ['.' => 'value']
        );

        return $options;
    }

    /**
     * @param \Symfony\Component\Validator\Constraint[] $constraints
     *
     * @return \Ibexa\Taxonomy\Form\Type\Validation\Constraint\Nested
     */
    private function createNestedConstraints(array $constraints): Nested
    {
        return new Nested(
            static function (?FieldData $value): ?string {
                if ($value === null || $value->value === null) {
                    return null;
                }

                return $value->value->text;
            },
            $constraints
        );
    }
}
