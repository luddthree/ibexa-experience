<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\VersionComparison\Form\Type;

use Ibexa\Bundle\VersionComparison\Form\ChoiceList\Loader\VersionInfoChoiceLoader;
use Ibexa\Bundle\VersionComparison\Form\Data\ValueChoice;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\VersionComparison\UI\VersionStatus;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class VersionInfoChoiceType extends AbstractType
{
    private ContentService $contentService;

    private VersionStatus $versionStatus;

    private TranslatorInterface $translator;

    private LanguageService $languageService;

    public function __construct(
        ContentService $contentService,
        VersionStatus $versionStatus,
        TranslatorInterface $translator,
        LanguageService $languageService
    ) {
        $this->contentService = $contentService;
        $this->versionStatus = $versionStatus;
        $this->translator = $translator;
        $this->languageService = $languageService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'version_info',
            ChoiceType::class,
            [
                'choice_loader' => new VersionInfoChoiceLoader(
                    $this->contentService,
                    $this->versionStatus,
                    $this->translator,
                    $this->languageService,
                    $options['content_info'],
                    $options['language']
                ),
                'placeholder' => $options['placeholder'] ?? $this->translator->trans(
                    /** @Desc("Select a version to compare") */
                    'version_info.comparison.select_placeholder',
                    [],
                    'ibexa_fieldtypes_comparison_ui'
                ),
                'property_path' => 'value',
                'required' => true,
                'label' => false,
                'choice_filter' => static function (?string $choice) use ($options): bool {
                    return $options['version_no'] !== $choice;
                },
                'choice_attr' => static function (string $value): array {
                    $valueChoice = ValueChoice::fromString($value);

                    return [
                        'data-version-no' => $valueChoice->getVersionNo(),
                        'data-version-language-code' => $valueChoice->getLanguageCode(),
                    ];
                },
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(
                'content_info'
            )
            ->setAllowedTypes(
                'content_info',
                ContentInfo::class,
            )
            ->setDefined([
                'language',
                'placeholder',
                'version_no',
            ])
            ->setAllowedTypes('language', [Language::class, 'null'])
            ->setDefaults([
                'language' => null,
                'version_no' => null,
                'label' => false,
                'multiple' => false,
                'expanded' => false,
            ]);
    }
}

class_alias(VersionInfoChoiceType::class, 'EzSystems\EzPlatformVersionComparisonBundle\Form\Type\VersionInfoChoiceType');
