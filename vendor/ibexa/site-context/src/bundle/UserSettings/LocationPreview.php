<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteContext\UserSettings;

use Ibexa\Contracts\User\UserSetting\FormMapperInterface;
use Ibexa\Contracts\User\UserSetting\ValueDefinitionInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class LocationPreview implements ValueDefinitionInterface, FormMapperInterface
{
    public const IDENTIFIER = 'location_preview';

    public const ENABLED_OPTION = 'enabled';
    public const DISABLED_OPTION = 'disabled';

    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getName(): string
    {
        return $this->translator->trans(
            /** @Desc("Location preview") */
            'ibexa.site_context.settings.location_preview.name',
            [],
            'ibexa_site_context'
        );
    }

    public function getDescription(): string
    {
        return $this->translator->trans(
            /** @Desc("Location preview") */
            'ibexa.site_context.settings.location_preview.description',
            [],
            'ibexa_site_context'
        );
    }

    public function getDisplayValue(string $storageValue): string
    {
        switch ($storageValue) {
            case self::ENABLED_OPTION:
                return $this->getTranslatedOptionEnabled();
            case self::DISABLED_OPTION:
                return $this->getTranslatedOptionDisabled();
            default:
                throw new InvalidArgumentException(
                    '$storageValue',
                    sprintf('There is no \'%s\' option', $storageValue)
                );
        }
    }

    public function getDefaultValue(): string
    {
        return self::ENABLED_OPTION;
    }

    public function mapFieldForm(
        FormBuilderInterface $formBuilder,
        ValueDefinitionInterface $value
    ): FormBuilderInterface {
        return $formBuilder->create(
            'value',
            ChoiceType::class,
            [
                'multiple' => false,
                'required' => true,
                'label' => $this->getName(),
                'choices' => [
                    $this->getTranslatedOptionEnabled() => self::ENABLED_OPTION,
                    $this->getTranslatedOptionDisabled() => self::DISABLED_OPTION,
                ],
            ]
        );
    }

    private function getTranslatedOptionEnabled(): string
    {
        return $this->translator->trans(
            /** @Desc("Enabled") */
            'ibexa.site_context.settings.location_preview.value.enabled',
            [],
            'ibexa_site_context'
        );
    }

    private function getTranslatedOptionDisabled(): string
    {
        return $this->translator->trans(
            /** @Desc("Disabled") */
            'ibexa.site_context.settings.location_preview.value.disabled',
            [],
            'ibexa_site_context'
        );
    }
}
