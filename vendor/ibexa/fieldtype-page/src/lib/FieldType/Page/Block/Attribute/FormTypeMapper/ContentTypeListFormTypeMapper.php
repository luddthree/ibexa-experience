<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\Page\Block\Attribute\FormTypeMapper;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Attribute\FormTypeMapper\AttributeFormTypeMapperInterface;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition;
use Ibexa\FieldTypePage\Form\Type\BlockAttribute\AttributeContentTypeListType;
use Symfony\Component\Form\FormBuilderInterface;

class ContentTypeListFormTypeMapper implements AttributeFormTypeMapperInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    /**
     * @param \Ibexa\Contracts\Core\Repository\ContentTypeService $contentTypeService
     */
    public function __construct(ContentTypeService $contentTypeService)
    {
        $this->contentTypeService = $contentTypeService;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $formBuilder
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition $blockDefinition
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition $blockAttributeDefinition
     * @param array $constraints
     *
     * @return \Symfony\Component\Form\FormBuilderInterface
     */
    public function map(
        FormBuilderInterface $formBuilder,
        BlockDefinition $blockDefinition,
        BlockAttributeDefinition $blockAttributeDefinition,
        array $constraints = []
    ): FormBuilderInterface {
        $contentTypeGroups = $this->contentTypeService->loadContentTypeGroups();

        $contentTypeList = [];
        foreach ($contentTypeGroups as $contentTypeGroup) {
            $contentTypes = $this->contentTypeService->loadContentTypes($contentTypeGroup);
            foreach ($contentTypes as $contentType) {
                $contentTypeList[$contentTypeGroup->identifier][$contentType->getName()] = $contentType->identifier;
            }
        }

        return $formBuilder->create(
            'value',
            AttributeContentTypeListType::class,
            [
                'choices' => $contentTypeList,
                'constraints' => $constraints,
            ]
        );
    }
}

class_alias(ContentTypeListFormTypeMapper::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Block\Attribute\FormTypeMapper\ContentTypeListFormTypeMapper');
