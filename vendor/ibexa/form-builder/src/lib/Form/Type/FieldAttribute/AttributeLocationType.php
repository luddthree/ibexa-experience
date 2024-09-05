<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Type\FieldAttribute;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\LocationService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class AttributeLocationType extends AbstractType
{
    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['value_as_location'] = null;

        if ($view->vars['value']) {
            try {
                $view->vars['value_as_location'] = $this->locationService->loadLocation(
                    (int)$view->vars['value']
                );
            } catch (NotFoundException | UnauthorizedException $e) {
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return TextType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'field_configuration_attribute_location';
    }
}

class_alias(AttributeLocationType::class, 'EzSystems\EzPlatformFormBuilder\Form\Type\FieldAttribute\AttributeLocationType');
