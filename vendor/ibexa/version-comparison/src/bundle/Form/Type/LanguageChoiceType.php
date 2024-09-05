<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\VersionComparison\Form\Type;

use Ibexa\AdminUi\Form\Type\Language\ConfiguredLanguagesChoiceType;
use JMS\TranslationBundle\Annotation\Ignore;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

final class LanguageChoiceType extends ConfiguredLanguagesChoiceType implements TranslationContainerInterface
{
    private const PLACEHOLDER = 'version_info.language.select_placeholder';
    private const DOMAIN = 'ibexa_fieldtypes_comparison_ui';

    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'placeholder' => $this->translator->trans(/** @Ignore */self::PLACEHOLDER, [], self::DOMAIN),
                'required' => false,
            ]);

        parent::configureOptions($resolver);
    }

    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::PLACEHOLDER, self::DOMAIN))->setDesc(
                'Language select placeholder'
            ),
        ];
    }
}
