<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Form\Type\BlockAttribute;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AttributeLocationListType extends AbstractType
{
    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    public function __construct(
        LocationService $locationService,
        ContentTypeService $contentTypeService
    ) {
        $this->locationService = $locationService;
        $this->contentTypeService = $contentTypeService;
    }

    public function getParent(): string
    {
        return TextType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'block_configuration_attribute_location_list';
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired(['match'])
            ->setAllowedTypes('match', ['array']);
    }

    /**
     * @param \Symfony\Component\Form\FormView $view
     * @param \Symfony\Component\Form\FormInterface $form
     * @param array $options
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['match'] = $options['match'];

        $attributeValue = $form->getData();

        if (empty($attributeValue)) {
            $view->vars += [
                'locations' => [],
                'content_types' => [],
            ];
        } else {
            $contentTypes = [];
            $locations = [];
            $locationIds = explode(',', $attributeValue);
            $locationsList = $this->locationService->loadLocationList($locationIds);

            $contentTypeIds = array_map(static function ($location) {
                return $location->contentInfo->contentTypeId;
            }, $locationsList);

            foreach ($locationsList as $location) {
                $locations[$location->id] = $location;
            }

            foreach ($contentTypeIds as $contentTypeId) {
                $contentTypes[$contentTypeId] = $this->contentTypeService->loadContentType($contentTypeId);
            }

            $view->vars += [
                'locations' => $locations,
                'content_types' => $contentTypes,
            ];
        }
    }
}

class_alias(AttributeLocationListType::class, 'EzSystems\EzPlatformPageFieldType\Form\Type\BlockAttribute\AttributeLocationListType');
