<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Seo\Content;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Seo\FieldType\SeoType;

/**
 * @internal
 */
final class SeoFieldResolver implements SeoFieldResolverInterface
{
    public function getSeoField(Content $content): ?Field
    {
        $seoField = null;

        foreach ($content->getFields() as $field) {
            if ($field->fieldTypeIdentifier !== SeoType::IDENTIFIER) {
                continue;
            }

            $seoField = $field;
        }

        return $seoField;
    }
}
