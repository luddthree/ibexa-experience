<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form;

use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

abstract class ContentFormFactory
{
    protected const IBEXA_CONTENT_EDIT_FORM_NAME = 'ezplatform_content_forms_content_edit';

    protected FormFactoryInterface $formFactory;

    protected CorporateAccountConfiguration $corporateAccount;

    public function __construct(
        FormFactoryInterface $formFactory,
        CorporateAccountConfiguration $corporateAccount
    ) {
        $this->formFactory = $formFactory;
        $this->corporateAccount = $corporateAccount;
    }

    /**
     * @phpstan-param class-string<\Symfony\Component\Form\FormTypeInterface<mixed>> $type
     *
     * @param mixed $data
     * @param array<string, mixed> $options
     */
    public function createContentEditForm(string $type, $data = null, array $options = []): FormInterface
    {
        return $this->formFactory->createNamed(self::IBEXA_CONTENT_EDIT_FORM_NAME, $type, $data, $options);
    }
}
