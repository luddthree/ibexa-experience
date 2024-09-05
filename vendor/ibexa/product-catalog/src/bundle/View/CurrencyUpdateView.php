<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\View;

use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Core\MVC\Symfony\View\BaseView;
use Symfony\Component\Form\FormInterface;

final class CurrencyUpdateView extends BaseView
{
    private CurrencyInterface $currency;

    private FormInterface $form;

    public function __construct(string $templateIdentifier, CurrencyInterface $currency, FormInterface $form)
    {
        parent::__construct($templateIdentifier);

        $this->currency = $currency;
        $this->form = $form;
    }

    public function getCurrency(): CurrencyInterface
    {
        return $this->currency;
    }

    public function setCurrency(CurrencyInterface $currency): void
    {
        $this->currency = $currency;
    }

    public function getForm(): FormInterface
    {
        return $this->form;
    }

    public function setForm(FormInterface $form): void
    {
        $this->form = $form;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getInternalParameters(): array
    {
        return [
            'currency' => $this->currency,
            'form' => $this->form->createView(),
        ];
    }
}
