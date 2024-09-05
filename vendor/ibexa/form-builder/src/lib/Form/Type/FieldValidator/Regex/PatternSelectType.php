<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Type\FieldValidator\Regex;

use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class PatternSelectType extends AbstractType
{
    public const PATTERN_NONE = 'none';
    public const PATTERN_DEFAULT = '/.*/';
    public const PATTERN_ALPHA = '/^[a-zA-Z .]*$/';
    public const PATTERN_ALNUM = '/^[a-zA-Z0-9 .]*$/';

    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    /**
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return string|null
     */
    public function getParent(): ?string
    {
        return ChoiceType::class;
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $choices = [
            $this->translator->trans(
                /** @Desc("None") */
                'form_builder.validator.regex.pattern.none',
                [],
                'ibexa_form_builder'
            ) => self::PATTERN_NONE,
            $this->translator->trans(
                /** @Desc("Alphabets") */
                'form_builder.validator.regex.pattern.alpha',
                [],
                'ibexa_form_builder'
            ) => self::PATTERN_ALPHA,
            $this->translator->trans(
                /** @Desc("Alphanumeric") */
                'form_builder.validator.regex.pattern.alnum',
                [],
                'ibexa_form_builder'
            ) => self::PATTERN_ALNUM,
            $this->translator->trans(
                /** @Desc("Custom") */
                'form_builder.validator.regex.pattern.custom',
                [],
                'ibexa_form_builder'
            ) => self::PATTERN_DEFAULT,
        ];

        $resolver->setDefaults([
            'choices' => $choices,
            'translation_domain' => 'ibexa_form_builder',
        ]);

        parent::configureOptions($resolver);
    }
}

class_alias(PatternSelectType::class, 'EzSystems\EzPlatformFormBuilder\Form\Type\FieldValidator\Regex\PatternSelectType');
