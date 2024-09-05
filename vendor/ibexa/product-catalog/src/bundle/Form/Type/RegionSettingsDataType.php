<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use Ibexa\Contracts\ProductCatalog\RegionServiceInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

final class RegionSettingsDataType extends AbstractType
{
    private RegionServiceInterface $regionService;

    public function __construct(RegionServiceInterface $regionService)
    {
        $this->regionService = $regionService;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $data = $form->getData();
        assert(array_key_exists('region_identifier', $data));

        $view->vars['region'] = $data['region_identifier'];
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
                $data = $event->getData();
                assert(array_key_exists('region_identifier', $data));

                $form = $event->getForm();
                $form->add('vat_category_identifier', VatCategoryChoiceType::class, [
                    'required' => false,
                    'region' => $this->regionService->getRegion($data['region_identifier']),
                ]);
            })
        ;
    }
}
