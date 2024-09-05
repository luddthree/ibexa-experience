<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\View;

use Ibexa\Core\MVC\Symfony\View\BaseView;
use Symfony\Component\Form\FormInterface;

final class AttributeDefinitionListView extends BaseView
{
    /** @var iterable<\Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface> */
    private iterable $attributesDefinitions;

    private FormInterface $searchForm;

    private bool $editable = false;

    /**
     * @param iterable<\Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface> $attributesDefinitions
     */
    public function __construct(
        $templateIdentifier,
        iterable $attributesDefinitions,
        FormInterface $searchForm
    ) {
        parent::__construct($templateIdentifier);

        $this->attributesDefinitions = $attributesDefinitions;
        $this->searchForm = $searchForm;
    }

    /**
     * @return iterable<\Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface>
     */
    public function getAttributesDefinitions(): iterable
    {
        return $this->attributesDefinitions;
    }

    /**
     * @param iterable<\Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface> $attributesDefinitions
     */
    public function setAttributesDefinitions(iterable $attributesDefinitions): void
    {
        $this->attributesDefinitions = $attributesDefinitions;
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
            'attributes_definitions' => $this->attributesDefinitions,
            'is_editable' => $this->editable,
            'search_form' => $this->searchForm->createView(),
        ];
    }
}
