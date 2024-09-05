<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Type\FieldAttribute\Action;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Core\FieldType\Image\Type as ImageFieldType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationType extends AbstractType
{
    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \Ibexa\Core\FieldType\Image\Type */
    private $imageFieldType;

    /**
     * @param \Ibexa\Contracts\Core\Repository\LocationService $locationService
     * @param \Ibexa\Contracts\Core\Repository\ContentTypeService $contentTypeService
     * @param \Ibexa\Core\FieldType\Image\Type $imageFieldType
     */
    public function __construct(
        LocationService $locationService,
        ContentTypeService $contentTypeService,
        ImageFieldType $imageFieldType
    ) {
        $this->locationService = $locationService;
        $this->contentTypeService = $contentTypeService;
        $this->imageFieldType = $imageFieldType;
    }

    /**
     * @return string|null
     */
    public function getParent()
    {
        return HiddenType::class;
    }

    /**
     * @param \Symfony\Component\Form\FormView $view
     * @param \Symfony\Component\Form\FormInterface $form
     * @param array $options
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $attributeValue = $form->getData();

        if (empty($attributeValue)) {
            $view->vars += [
                'location' => null,
                'content_type' => null,
                'content_image_field' => null,
            ];
        } else {
            $locationId = (int) $attributeValue;

            $location = $this->locationService->loadLocation($locationId);
            $contentType = $this->contentTypeService->loadContentType($location->contentInfo->contentTypeId);
            $contentImageField = $this->getContentImageField($location->getContent(), $contentType);

            $view->vars += [
                'location' => $location,
                'content_type' => $contentType,
                'content_image_field' => $contentImageField,
            ];
        }
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Field|null
     */
    protected function getContentImageField(Content $content, ContentType $contentType): ?Field
    {
        foreach ($contentType->getFieldDefinitions() as $fieldDefinition) {
            if ($fieldDefinition->fieldTypeIdentifier == $this->imageFieldType->getFieldTypeIdentifier()) {
                return $content->getField($fieldDefinition->identifier);
            }
        }

        return null;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'error_bubbling' => false,
            ],
        );
    }
}

class_alias(LocationType::class, 'EzSystems\EzPlatformFormBuilder\Form\Type\FieldAttribute\Action\LocationType');
