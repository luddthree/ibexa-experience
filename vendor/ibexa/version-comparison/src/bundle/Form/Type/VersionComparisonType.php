<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\VersionComparison\Form\Type;

use Ibexa\AdminUi\Form\Type\ChoiceList\Loader\ConfiguredLanguagesChoiceLoader;
use Ibexa\Bundle\VersionComparison\Form\ChoiceList\Loader\LanguageChoiceLoader;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class VersionComparisonType extends AbstractType
{
    private ContentService $contentService;

    private ConfiguredLanguagesChoiceLoader $languageChoiceLoader;

    public function __construct(ContentService $contentService, ConfiguredLanguagesChoiceLoader $languageChoiceLoader)
    {
        $this->contentService = $contentService;
        $this->languageChoiceLoader = $languageChoiceLoader;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'language',
                LanguageChoiceType::class,
                [
                    'choice_loader' => new LanguageChoiceLoader(
                        $this->languageChoiceLoader,
                        $this->contentService,
                        $options['content_info']
                    ),
                ]
            )
            ->add(
                'version_a',
                VersionInfoChoiceType::class,
                [
                    'content_info' => $options['content_info'],
                    'placeholder' => false,
                ]
            )
            ->add(
                'version_b',
                VersionInfoChoiceType::class,
                [
                    'content_info' => $options['content_info'],
                ]
            )
            ->add(
                'compare',
                ButtonType::class,
                [
                    'label' => false,
                ]
            )
            ->add(
                'side_by_side',
                ButtonType::class,
                [
                    'label' => false,
                ]
            );

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            static function (FormEvent $event) {
                /** @var \Ibexa\Bundle\VersionComparison\Form\Data\VersionComparisonData $data */
                $data = $event->getData();
                $form = $event->getForm();

                $language = $data->getLanguage();
                $versionA = $data->getVersionA();
                $contentInfo = $versionA->getVersionInfo()->contentInfo;

                $versionB = $data->getVersionB();
                if ($versionB->getVersionInfo() !== null) {
                    $form
                        ->add(
                            'compare',
                            ButtonType::class,
                            [
                                'label' => false,
                                'disabled' => $versionB->getLanguageCode() !== $versionA->getLanguageCode(),
                            ]
                        );
                }

                $data = [
                    'content_info' => $contentInfo,
                    'language' => $language,
                    'version_no' => $versionA->getValue(),
                ];

                if ($versionB->getVersionInfo() !== null) {
                    $data['placeholder'] = false;
                }
                $form
                    ->add(
                        'version_b',
                        VersionInfoChoiceType::class,
                        $data
                    );
                $form
                    ->add(
                        'version_a',
                        VersionInfoChoiceType::class,
                        [
                            'content_info' => $contentInfo,
                            'language' => $language,
                            'placeholder' => false,
                        ]
                    );
            }
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
                ContentInfo::class
            );
    }
}

class_alias(VersionComparisonType::class, 'EzSystems\EzPlatformVersionComparisonBundle\Form\Type\VersionComparisonType');
