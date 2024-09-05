<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\View\Matcher\ProductTypeBased;

use Ibexa\Bundle\ProductCatalog\View\Matcher\ProductTypeBased\IsProduct;
use Ibexa\ContentForms\Content\View\ContentTypeValueView;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Core\MVC\Symfony\View\BaseView;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Type as ProductSpecificationType;
use PHPUnit\Framework\TestCase;

final class IsProductTest extends TestCase
{
    public function testMatch(): void
    {
        self::assertTrue((new IsProduct(true))->match($this->createContentTypeValueView(true)));
        self::assertFalse((new IsProduct(true))->match($this->createContentTypeValueView(false)));
        self::assertTrue((new IsProduct(false))->match($this->createContentTypeValueView(false)));
        self::assertFalse((new IsProduct(false))->match($this->createContentTypeValueView(true)));
        self::assertFalse((new IsProduct(true))->match($this->createMock(View::class)));
    }

    /**
     * @phpstan-return \Ibexa\Core\MVC\Symfony\View\View&\Ibexa\ContentForms\Content\View\ContentTypeValueView
     */
    private function createContentTypeValueView(bool $withProductSpecification): View
    {
        $contentType = $this->createContentType($withProductSpecification);

        return new class($contentType) extends BaseView implements ContentTypeValueView {
            private ContentType $contentType;

            public function __construct(ContentType $contentType)
            {
                parent::__construct();

                $this->contentType = $contentType;
            }

            public function getContentType(): ContentType
            {
                return $this->contentType;
            }
        };
    }

    private function createContentType(bool $withProductSpecification): ContentType
    {
        $contentType = $this->createMock(ContentType::class);
        $contentType
            ->method('hasFieldDefinitionOfType')
            ->with(ProductSpecificationType::FIELD_TYPE_IDENTIFIER)
            ->willReturn($withProductSpecification);

        return $contentType;
    }
}
