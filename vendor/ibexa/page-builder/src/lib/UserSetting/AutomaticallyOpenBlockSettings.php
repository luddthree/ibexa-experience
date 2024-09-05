<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\UserSetting;

use Ibexa\Contracts\User\UserSetting\FormMapperInterface;
use Ibexa\Contracts\User\UserSetting\ValueDefinitionInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AutomaticallyOpenBlockSettings implements ValueDefinitionInterface, FormMapperInterface
{
    private const OPTION_ENABLED = 'enabled';
    private const OPTION_DISABLED = 'disabled';

    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getName(): string
    {
        return $this->translator->trans(
            /** @Desc("Automatically open block settings in builders") */
            'user.settings.automatically_open_block_settings.name',
            [],
            'ibexa_page_builder'
        );
    }

    public function getDescription(): string
    {
        return $this->translator->trans(
            /** @Desc("Automatically open block settings in builders") */
            'user.settings.automatically_open_block_settings.description',
            [],
            'ibexa_page_builder'
        );
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function getDisplayValue(string $storageValue): string
    {
        switch ($storageValue) {
            case self::OPTION_ENABLED:
                return $this->translator->trans(
                    /** @Desc("Enabled") */
                    'user.settings.automatically_open_block_settings.value.enabled',
                    [],
                    'ibexa_page_builder'
                );
            case self::OPTION_DISABLED:
                return $this->translator->trans(
                    /** @Desc("Disabled") */
                    'user.settings.automatically_open_block_settings.value.disabled',
                    [],
                    'ibexa_page_builder'
                );
            default:
                throw new InvalidArgumentException(
                    '$storageValue',
                    sprintf('There is no \'%s\' option', $storageValue)
                );
        }
    }

    public function getDefaultValue(): string
    {
        return self::OPTION_ENABLED;
    }

    public function mapFieldForm(
        FormBuilderInterface $formBuilder,
        ValueDefinitionInterface $value
    ): FormBuilderInterface {
        $labels = [
            /** @Desc("Enabled") */
            self::OPTION_ENABLED => 'user.settings.automatically_open_block_settings.value.enabled',
            /** @Desc("Disabled") */
            self::OPTION_DISABLED => 'user.settings.automatically_open_block_settings.value.disabled',
        ];

        return $formBuilder->create(
            'value',
            ChoiceType::class,
            [
                'multiple' => false,
                'required' => true,
                /** @Desc("Automatically open block settings in builders") */
                'label' => 'user.settings.automatically_open_block_settings.description',
                'translation_domain' => 'ibexa_page_builder',
                'choices' => [
                    self::OPTION_ENABLED,
                    self::OPTION_DISABLED,
                ],
                'choice_label' => static fn (string $choice): string => $labels[$choice],
            ],
        );
    }
}
