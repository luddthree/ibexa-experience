<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ContentTree\Component;

use Ibexa\AdminUi\Component\TwigComponent;
use Ibexa\AdminUi\Form\Factory\FormFactory;
use Symfony\Component\Form\FormInterface;
use Twig\Environment;

abstract class FormComponent extends TwigComponent
{
    protected FormFactory $formFactory;

    /**
     * @param array<string, mixed> $parameters
     */
    public function __construct(
        Environment $twig,
        FormFactory $formFactory,
        string $template,
        array $parameters = []
    ) {
        $this->formFactory = $formFactory;

        parent::__construct($twig, $template, $parameters);
    }

    abstract public function getForm(): FormInterface;

    /**
     * @param array<string, mixed> $parameters
     */
    public function render(array $parameters = []): string
    {
        return parent::render($parameters + [
            'form' => $this->getForm()->createView(),
        ]);
    }
}
