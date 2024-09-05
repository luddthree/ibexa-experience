<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type\Model;

use Ibexa\Personalization\Service\Search\SearchServiceInterface;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class EditorContentListType extends AbstractType
{
    private SearchServiceInterface $searchService;

    private SettingServiceInterface $settingService;

    public function __construct(
        SearchServiceInterface $searchService,
        SettingServiceInterface $settingService
    ) {
        $this->searchService = $searchService;
        $this->settingService = $settingService;
    }

    public function getBlockPrefix(): string
    {
        return 'personalization_model_editorial_list';
    }

    public function getParent(): string
    {
        return CollectionType::class;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $editorContentList = $form->getData();
        $contentItems = [];

        /** @var \Ibexa\Personalization\Form\Data\EditorContentData $editorContent */
        foreach ($editorContentList as $editorContent) {
            $id = $editorContent->getId();
            $contentItems[$id] = $this->getName((string) $id);
        }

        $view->vars += [
            'contentItems' => $contentItems,
        ];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'entry_type' => EditorContentType::class,
            'prototype' => true,
            'allow_add' => true,
            'allow_delete' => true,
            'entry_options' => [
                'label' => false,
            ],
        ]);
    }

    private function getName(string $contentId): string
    {
        $customerId = $this->settingService->getCustomerIdFromRequest();
        if (null === $customerId) {
            return $contentId;
        }

        $resultList = $this->searchService->searchAttributes($customerId, $contentId);
        foreach ($resultList as $searchHit) {
            return $searchHit->getValue();
        }

        return $contentId;
    }
}

class_alias(EditorContentListType::class, 'Ibexa\Platform\Personalization\Form\Type\Model\EditorContentListType');
