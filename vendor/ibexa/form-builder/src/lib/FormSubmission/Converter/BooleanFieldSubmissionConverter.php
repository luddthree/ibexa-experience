<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\FormSubmission\Converter;

use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class BooleanFieldSubmissionConverter extends GenericFieldSubmissionConverter
{
    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    /**
     * @param string $typeIdentifier
     * @param \Twig\Environment $twig
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator
     */
    public function __construct(
        string $typeIdentifier,
        Environment $twig,
        TranslatorInterface $translator
    ) {
        parent::__construct($typeIdentifier, $twig);

        $this->translator = $translator;
    }

    /**
     * @param string $fieldValue
     *
     * @return string
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function toDisplayValue($fieldValue): string
    {
        return parent::toDisplayValue(
            $fieldValue
            ? /** @Desc("True") */
            $this->translator->trans('form_builder.submission.boolean.true', [], 'ibexa_form_builder')
            : /** @Desc("False") */
            $this->translator->trans('form_builder.submission.boolean.false', [], 'ibexa_form_builder')
        );
    }

    /**
     * @param $fieldValue
     *
     * @return string
     */
    public function toExportValue($fieldValue): string
    {
        return $fieldValue ? 'True' : 'False';
    }
}

class_alias(BooleanFieldSubmissionConverter::class, 'EzSystems\EzPlatformFormBuilder\FormSubmission\Converter\BooleanFieldSubmissionConverter');
