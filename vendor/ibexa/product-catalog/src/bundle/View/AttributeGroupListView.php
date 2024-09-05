<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\View;

use Ibexa\Core\MVC\Symfony\View\BaseView;
use Symfony\Component\Form\FormInterface;

final class AttributeGroupListView extends BaseView
{
    /** @var iterable<\Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface> */
    private iterable $attributeGroups;

    private FormInterface $searchForm;

    private bool $editable = false;

    /**
     * @param iterable<\Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface> $attributeGroups
     */
    public function __construct($templateIdentifier, iterable $attributeGroups, FormInterface $searchForm)
    {
        parent::__construct($templateIdentifier);

        $this->attributeGroups = $attributeGroups;
        $this->searchForm = $searchForm;
    }

    /**
     * @return iterable<\Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface>
     */
    public function getAttributeGroups(): iterable
    {
        return $this->attributeGroups;
    }

    /**
     * @param iterable<\Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface> $attributeGroups
     */
    public function setAttributeGroups(iterable $attributeGroups): void
    {
        $this->attributeGroups = $attributeGroups;
    }

    public function getSearchForm(): FormInterface
    {
        return $this->searchForm;
    }

    public function setSearchForm(FormInterface $searchForm): void
    {
        $this->searchForm = $searchForm;
    }

    public function isEditable(): bool
    {
        return $this->editable;
    }

    public function setEditable(bool $editable): void
    {
        $this->editable = $editable;
    }

    /**
     * @return array<string,mixed>
     */
    protected function getInternalParameters(): array
    {
        return [
            'attribute_groups' => $this->attributeGroups,
            'is_editable' => $this->editable,
            'search_form' => $this->searchForm->createView(),
        ];
    }
}
