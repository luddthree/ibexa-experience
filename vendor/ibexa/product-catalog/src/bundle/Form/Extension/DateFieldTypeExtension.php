<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Extension;

use Ibexa\ContentForms\Form\Type\FieldType\DateFieldType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RequestStack;

final class DateFieldTypeExtension extends AbstractTypeExtension
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($request !== null && $request->attributes->get('_route') === 'ibexa.product_catalog.product.edit') {
            $view->vars['isEditView'] = true;
        }
    }

    public static function getExtendedTypes(): iterable
    {
        return [DateFieldType::class];
    }
}
