<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\LandingPage\Mapper;

use Ibexa\AdminUi\FieldType\FieldDefinitionFormMapperInterface;
use Ibexa\AdminUi\Form\Data\FieldDefinitionData;
use Ibexa\FieldTypePage\ApplicationConfig\Providers\BlockDefinitionsInterface;
use Ibexa\FieldTypePage\ApplicationConfig\Providers\LayoutDefinitionsInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class LandingPageFormMapper implements FieldDefinitionFormMapperInterface
{
    public const PAGE_VIEW_MODE = 'page_view_mode';
    public const FIELD_VIEW_MODE = 'field_view_mode';
    public const VIEW_MODE_ICONS_MAP = [
        self::PAGE_VIEW_MODE => 'landing_page',
        self::FIELD_VIEW_MODE => 'fields',
    ];

    /** @var \Ibexa\FieldTypePage\ApplicationConfig\Providers\BlockDefinitionsInterface */
    private $blockDefinitions;

    /** @var \Ibexa\FieldTypePage\ApplicationConfig\Providers\LayoutDefinitionsInterface */
    private $layoutDefinitions;

    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    public function __construct(
        BlockDefinitionsInterface $blockDefinitions,
        LayoutDefinitionsInterface $layoutDefinitions,
        TranslatorInterface $translator
    ) {
        $this->blockDefinitions = $blockDefinitions;
        $this->layoutDefinitions = $layoutDefinitions;
        $this->translator = $translator;
    }

    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data): void
    {
        $blocksConfiguration = $this->blockDefinitions->getConfig();
        $choices = $this->prepareChoices($blocksConfiguration);
        $labels = $this->prepareLabels($blocksConfiguration);
        $thumbnailsList = array_column($blocksConfiguration, 'thumbnail', 'type');
        $fieldDefinitionForm
            ->add(
                'availableBlocks',
                ChoiceType::class,
                [
                    'property_path' => 'fieldSettings[availableBlocks]',
                    'expanded' => true,
                    'multiple' => true,
                    'choices' => $choices,
                    'data' => $data->fieldSettings['availableBlocks'] === null
                        ? array_column($blocksConfiguration, 'type')
                        : $data->fieldSettings['availableBlocks'],
                    'choice_attr' => static function ($choice) use ($thumbnailsList) {
                        return ['thumbnail' => $thumbnailsList[$choice]];
                    },
                    'choice_label' => static function ($choice) use ($labels) {
                        return $labels[$choice];
                    },
                ]
            );

        $visibleLayouts = array_filter(
            $this->layoutDefinitions->getConfig(),
            static function (array $layout): bool {
                return $layout['visible'];
            }
        );

        $layoutsLabels = array_column(
            $visibleLayouts,
            'name',
            'id'
        );

        $layoutsChoices = array_column(
            $visibleLayouts,
            'id',
            'id'
        );

        $layoutsThumbnailsList = array_column(
            $visibleLayouts,
            'thumbnail',
            'id'
        );

        $fieldDefinitionForm->add(
            'availableLayouts',
            ChoiceType::class,
            [
                'property_path' => 'fieldSettings[availableLayouts]',
                'expanded' => true,
                'multiple' => true,
                'choices' => $layoutsChoices,
                'data' => $data->fieldSettings['availableLayouts'] ?? $layoutsChoices,
                'choice_attr' => static function ($choice) use ($layoutsThumbnailsList): array {
                    return ['thumbnail' => $layoutsThumbnailsList[$choice]];
                },
                'choice_label' => static function ($choice) use ($layoutsLabels): string {
                    return $layoutsLabels[$choice];
                },
            ]
        );

        $fieldDefinitionForm->add(
            'editorMode',
            ChoiceType::class,
            [
                'property_path' => 'fieldSettings[editorMode]',
                'expanded' => true,
                'multiple' => false,
                'choices' => [
                    $this->translator->trans(
                        /** @Desc("Page view") */
                        'landing_page_ft.settings.edit_mode.page_view'
                    ) => self::PAGE_VIEW_MODE,
                    $this->translator->trans(
                        /** @Desc("Field view") */
                        'landing_page_ft.settings.edit_mode.field_view'
                    ) => self::FIELD_VIEW_MODE,
                ],
                'data' => $data->fieldSettings['editorMode'] ?? self::PAGE_VIEW_MODE,
                'choice_attr' => static function ($choice) {
                    return ['thumbnail' => self::VIEW_MODE_ICONS_MAP[$choice]];
                },
            ]
        );
    }

    /**
     * @return array<string,array<string,string>>
     */
    private function prepareChoices(array $configuration): array
    {
        $choices = [];

        foreach ($configuration as $blockConfiguration) {
            if ($blockConfiguration['visible']) {
                $category = $blockConfiguration['category'];
                $choices[$category][$blockConfiguration['type']] = $blockConfiguration['type'];
            }
        }

        return $choices;
    }

    /**
     * @return array<string,string>
     */
    private function prepareLabels(array $configuration): array
    {
        $labels = [];

        foreach ($configuration as $blockConfiguration) {
            $name = $blockConfiguration['name'];
            $labels[$blockConfiguration['type']] = $name;
        }

        return $labels;
    }
}

class_alias(LandingPageFormMapper::class, 'EzSystems\EzPlatformPageFieldType\FieldType\LandingPage\Mapper\LandingPageFormMapper');
