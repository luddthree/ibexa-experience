<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Form\Type\Extension\EventSubscriber;

use Ibexa\AdminUi\Form\Type\FieldDefinition\FieldDefinitionType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

/**
 * Modifies CT editing form by rebuilding field definition list with custom options on Taxonomy fieldtypes.
 *
 * This is currently used to disable `required`, `translatable` and `identifier` fields.
 */
final class DisableFieldDefinitionFieldsSubscriber implements EventSubscriberInterface
{
    private TaxonomyConfiguration $taxonomyConfiguration;

    public function __construct(
        TaxonomyConfiguration $taxonomyConfiguration
    ) {
        $this->taxonomyConfiguration = $taxonomyConfiguration;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => ['onPreSetData'],
        ];
    }

    public function onPreSetData(FormEvent $event): void
    {
        /** @var array<string, \Ibexa\AdminUi\Form\Data\FieldDefinitionData>|null $data */
        $data = $event->getData();
        $form = $event->getForm();

        if (null === $data) {
            return;
        }

        $contentTypeDraft = $this->getContentTypeFromChildren($data);

        if (
            null === $contentTypeDraft
            || !$this->taxonomyConfiguration->isContentTypeAssociatedWithTaxonomy($contentTypeDraft)
        ) {
            return;
        }

        $taxonomy = $this->taxonomyConfiguration->getTaxonomyForContentType($contentTypeDraft);
        $fieldMappings = $this->taxonomyConfiguration->getFieldMappings($taxonomy);

        foreach ($data as $fieldTypeIdentifier => $fieldTypeData) {
            $childForm = $form->get($fieldTypeIdentifier);
            $form->remove($childForm->getName());

            $options = $this->getModifiedOptions($childForm, $fieldMappings, $fieldTypeIdentifier);

            $form->add($childForm->getName(), FieldDefinitionType::class, $options);
        }
    }

    /**
     * @param array<string, \Ibexa\AdminUi\Form\Data\FieldDefinitionData> $data
     */
    private function getContentTypeFromChildren(array $data): ?ContentType
    {
        if (!empty($data)) {
            $child = reset($data);

            return $child->contentTypeData->contentTypeDraft;
        }

        return null;
    }

    /**
     * @param array<string, string> $fieldMappings
     *
     * @return array<string, mixed>
     */
    private function getModifiedOptions(
        FormInterface $childForm,
        array $fieldMappings,
        string $fieldTypeIdentifier
    ): array {
        $options = $childForm->getConfig()->getOptions();

        if (!in_array($fieldTypeIdentifier, $fieldMappings, true)) {
            return $options;
        }

        $options = array_merge(
            $options,
            [
                'disable_identifier_field' => true,
                'disable_required_field' => true,
                'disable_translatable_field' => true,
                'disable_remove' => true,
            ],
        );

        if ($fieldTypeIdentifier === $fieldMappings['name']) {
            $options['disable_translatable_field'] = false;
        }

        return $options;
    }
}
