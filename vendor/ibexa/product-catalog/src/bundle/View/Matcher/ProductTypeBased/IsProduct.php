<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\View\Matcher\ProductTypeBased;

use Ibexa\ContentForms\Content\View\ContentTypeValueView;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\ProductCatalog\ViewMatcher\ProductTypeBased\IsProduct as IsProductInterface;
use Ibexa\Core\MVC\Symfony\Matcher\ViewMatcherInterface;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Type;

final class IsProduct implements ViewMatcherInterface, IsProductInterface
{
    private bool $value;

    public function __construct(bool $value = true)
    {
        $this->value = $value;
    }

    public function match(View $view): bool
    {
        if (!$view instanceof ContentTypeValueView) {
            return false;
        }

        $isProductBased = $this->hasProductSpecificationFieldType($view->getContentType());

        return ($this->value && $isProductBased) || (!$this->value && !$isProductBased);
    }

    private function hasProductSpecificationFieldType(ContentType $contentType): bool
    {
        return $contentType->hasFieldDefinitionOfType(Type::FIELD_TYPE_IDENTIFIER);
    }

    public function setMatchingConfig($matchingConfig): void
    {
        $this->value = (bool)$matchingConfig;
    }
}
